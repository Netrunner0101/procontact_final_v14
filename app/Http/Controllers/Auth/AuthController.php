<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $credentials['email'];
        $key = 'login.' . $email;
        
        // Check if user is rate limited (too many failed attempts)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            // If blocked for more than 5 minutes, trigger password reset
            if ($seconds > 300) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $this->initiatePasswordReset($user);
                    return back()->withErrors([
                        'email' => __('Too many failed attempts. A password reset email has been sent.'),
                    ]);
                }
            }
            
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Clear the rate limiter on successful login
            RateLimiter::clear($key);
            
            // Update last login time
            $user = Auth::user();
            $user->update(['last_login_at' => Carbon::now()]);
            
            $welcome = __('Welcome back, :name!', ['name' => $user->prenom ?: $user->nom ?: $user->email]);

            // Redirect based on user role
            if ($user->isClient()) {
                return redirect()->intended(route('client.dashboard'))->with('success', $welcome);
            }

            return redirect()->intended('dashboard')->with('success', $welcome);
        }

        // Increment failed attempts
        RateLimiter::hit($key, 300); // 5 minutes decay
        
        return back()->withErrors([
            'email' => __('The provided credentials do not match.'),
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        return view('auth.register', [
            'pendingOauth' => $request->session()->get('pending_oauth'),
        ]);
    }

    public function register(Request $request)
    {
        $pending = $request->session()->get('pending_oauth');

        if ($pending) {
            return $this->confirmOauthRegistration($request, $pending);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $adminRole = Role::where('nom', Role::ADMIN)->first();

        if (!$adminRole) {
            return back()->withErrors(['email' => __('The system is not yet configured. Please contact the administrator.')]);
        }

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $adminRole->id,
        ]);

        Auth::login($user);

        return redirect('dashboard')->with('success', __('Your account has been created successfully. Welcome to Pro Contact!'));
    }

    private function confirmOauthRegistration(Request $request, array $pending)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:50',
        ]);

        // The email is verified by the OAuth provider and not editable in the
        // confirmation form, but another user could have registered the same
        // address between the OAuth start and this confirmation.
        if (User::where('email', $pending['email'])->exists()) {
            $request->session()->forget('pending_oauth');
            return redirect()->route('login')
                ->with('error', __('An account with this email already exists. Please sign in.'));
        }

        $adminRole = Role::where('nom', Role::ADMIN)->first();

        if (!$adminRole) {
            return back()->withErrors(['email' => __('The system is not yet configured. Please contact the administrator.')]);
        }

        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $pending['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password' => Str::random(32),
            'provider' => $pending['provider'],
            'avatar' => $pending['avatar'] ?? null,
            'email_verified_at' => Carbon::now(),
            'role_id' => $adminRole->id,
        ];

        if ($pending['provider'] === 'google' && isset($pending['google_id'])) {
            $userData['google_id'] = $pending['google_id'];
        }

        if ($pending['provider'] === 'apple' && isset($pending['apple_id'])) {
            $userData['apple_id'] = $pending['apple_id'];
        }

        $user = User::create($userData);

        $request->session()->forget('pending_oauth');
        Auth::login($user);

        $providerName = ucfirst($pending['provider']);

        return redirect('dashboard')->with('success', __('Account created with :provider. Welcome to Pro Contact!', ['provider' => $providerName]));
    }

    public function cancelOauthRegistration(Request $request)
    {
        $request->session()->forget('pending_oauth');

        return redirect()->route('login')
            ->with('status', __('Sign-up cancelled. You can sign in or register at any time.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', __('You have been logged out successfully.'));
    }
    
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
    
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $this->initiatePasswordReset($user);
        }
        
        return back()->with('status', __('If your email exists, you will receive a reset link.'));
    }
    
    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::where('email', $request->email)
                   ->where('password_reset_token', $request->token)
                   ->where('password_reset_expires', '>', Carbon::now())
                   ->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => __('This reset link is invalid or has expired.'),
            ]);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_expires' => null,
        ]);
        
        // Clear any rate limiting
        RateLimiter::clear('login.' . $user->email);
        
        return redirect()->route('login')->with('status', __('Your password has been reset successfully.'));
    }
    
    private function initiatePasswordReset(User $user)
    {
        $token = Str::random(60);
        
        $user->update([
            'password_reset_token' => $token,
            'password_reset_expires' => Carbon::now()->addHours(1),
        ]);
        
        // Send password reset email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
        } catch (\Exception $e) {
            // Log the error but don't expose it to the user
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }
}
