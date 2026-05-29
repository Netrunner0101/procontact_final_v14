<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Mail\PasswordResetMail;
use App\Mail\RgpdConsentMail;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

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
        $key = 'login.'.$email;

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
            $user = Auth::user();

            // Block sign-in until the email address has been confirmed
            // (OAuth users are pre-verified and bypass this check).
            if ($user->requiresEmailVerification()) {
                Auth::logout();
                $request->session()->put('verification.email', $user->email);

                return redirect()->route('verification.notice')
                    ->with('error', __('Please confirm your email address before logging in. Check your inbox for the verification link.'));
            }

            $request->session()->regenerate();

            // Clear the rate limiter on successful login
            RateLimiter::clear($key);

            // Update last login time
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

        $emailRules = ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'];
        if (app()->environment('production')) {
            $emailRules[] = 'email:dns';
        }

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'prenom' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'email' => $emailRules,
            'telephone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\s().\-]*$/'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => ['accepted'],
        ], [
            'nom.regex' => __('The last name may only contain letters, spaces, apostrophes and hyphens.'),
            'prenom.regex' => __('The first name may only contain letters, spaces, apostrophes and hyphens.'),
            'telephone.regex' => __('The phone number contains invalid characters.'),
            'terms.accepted' => __('You must accept the Privacy Policy and Terms of Service to create an account.'),
        ]);

        $adminRole = Role::where('nom', Role::ADMIN)->first();

        if (! $adminRole) {
            return back()->withErrors(['email' => __('The system is not yet configured. Please contact the administrator.')]);
        }

        $token = Str::random(64);
        $rgpdVersion = config('app.rgpd_version', '2026-05');

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $adminRole->id,
            'email_verified_at' => null,
            'email_verification_token' => $token,
            'email_verification_expires' => Carbon::now()->addHours(24),
            'email_verification_sent_at' => Carbon::now(),
            'terms_accepted_at' => Carbon::now(),
            'terms_accepted_version' => $rgpdVersion,
        ]);

        if (! empty($validated['telephone'])) {
            $user->numeroTelephones()->create(['numero_telephone' => $validated['telephone']]);
        }

        $this->recordGdprConsent($user, true, $request, $rgpdVersion);
        $this->sendVerificationEmail($user, $token);
        $this->sendRgpdConsentEmail($user, true, $rgpdVersion);

        // Do NOT log the user in — they must confirm the email first.
        $request->session()->put('verification.email', $user->email);

        return redirect()->route('register.success');
    }

    private function confirmOauthRegistration(Request $request, array $pending)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => __('You must accept the Privacy Policy and Terms of Service to create an account.'),
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

        if (! $adminRole) {
            return back()->withErrors(['email' => __('The system is not yet configured. Please contact the administrator.')]);
        }

        $rgpdVersion = config('app.rgpd_version', '2026-05');

        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $pending['email'],
            'password' => Str::random(32),
            'provider' => $pending['provider'],
            'avatar' => $pending['avatar'] ?? null,
            'email_verified_at' => Carbon::now(),
            'role_id' => $adminRole->id,
            'terms_accepted_at' => Carbon::now(),
            'terms_accepted_version' => $rgpdVersion,
        ];

        if ($pending['provider'] === 'google' && isset($pending['google_id'])) {
            $userData['google_id'] = $pending['google_id'];
        }

        if ($pending['provider'] === 'apple' && isset($pending['apple_id'])) {
            $userData['apple_id'] = $pending['apple_id'];
        }

        $user = User::create($userData);

        if (! empty($validated['telephone'])) {
            $user->numeroTelephones()->create(['numero_telephone' => $validated['telephone']]);
        }

        $this->recordGdprConsent($user, true, $request, $rgpdVersion);
        $this->sendRgpdConsentEmail($user, true, $rgpdVersion);

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

        if (! $user) {
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
        RateLimiter::clear('login.'.$user->email);

        return redirect()->route('login')->with('status', __('Your password has been reset successfully.'));
    }

    public function showRegisterSuccess(Request $request)
    {
        $email = $request->session()->get('verification.email');

        if (! $email) {
            return redirect()->route('login');
        }

        return view('auth.register-success', ['email' => $email]);
    }

    public function showVerificationNotice(Request $request)
    {
        return view('auth.verify-email', [
            'email' => $request->session()->get('verification.email'),
        ]);
    }

    public function verifyEmail(Request $request, string $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => __('This verification link is invalid or has already been used.'),
            ]);
        }

        if ($user->email_verification_expires && $user->email_verification_expires->isPast()) {
            return redirect()->route('verification.notice')
                ->with('error', __('This verification link has expired. Request a new one below.'))
                ->with('verification.email', $user->email);
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'email_verification_token' => null,
            'email_verification_expires' => null,
        ]);

        $request->session()->forget('verification.email');

        return redirect()->route('login')
            ->with('success', __('Your email has been verified. You can now log in.'));
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $key = 'verification-resend.'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
            ]);
        }
        RateLimiter::hit($key, 600);

        $user = User::where('email', $request->email)->first();

        // Always show the same response so we don't leak which addresses exist.
        if ($user && $user->requiresEmailVerification()) {
            $token = Str::random(64);
            $user->update([
                'email_verification_token' => $token,
                'email_verification_expires' => Carbon::now()->addHours(24),
                'email_verification_sent_at' => Carbon::now(),
            ]);
            $this->sendVerificationEmail($user, $token);
        }

        return back()->with('status', __('If your account exists and is not yet verified, a new confirmation email has been sent.'));
    }

    private function sendVerificationEmail(User $user, string $token): void
    {
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: '.$e->getMessage());
        }
    }

    private function sendRgpdConsentEmail(User $user, bool $consent, string $version): void
    {
        try {
            Mail::to($user->email)->send(new RgpdConsentMail($user, $consent, $version));
        } catch (\Exception $e) {
            Log::error('Failed to send GDPR consent email: '.$e->getMessage());
        }
    }

    private function recordGdprConsent(User $user, bool $consent, Request $request, string $version): void
    {
        Log::channel('rgpd')->info('GDPR consent recorded', [
            'user_id' => $user->id,
            'email' => $user->email,
            'consent' => $consent,
            'version' => $version,
            'accepted_at' => optional($user->terms_accepted_at)->toIso8601String() ?? now()->toIso8601String(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
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
            \Log::error('Failed to send password reset email: '.$e->getMessage());
        }
    }
}
