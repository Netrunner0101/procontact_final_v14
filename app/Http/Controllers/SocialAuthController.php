<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Update user info
                $user->update([
                    'avatar' => $googleUser->avatar,
                ]);

                Auth::login($user);
                return redirect()->route('dashboard')->with('success', __('Signed in with Google.'));
            }

            // Check if user exists with this email
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'provider' => 'google',
                    'avatar' => $googleUser->avatar,
                ]);

                Auth::login($existingUser);
                return redirect()->route('dashboard')->with('success', __('Google account linked. Welcome back!'));
            }

            // Create new user (admin role by default — entrepreneur)
            $adminRole = Role::where('nom', Role::ADMIN)->firstOrFail();

            $newUser = User::create([
                'nom' => $googleUser->user['family_name'] ?? '',
                'prenom' => $googleUser->user['given_name'] ?? $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'avatar' => $googleUser->avatar,
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]);

            Auth::login($newUser);
            return redirect()->route('dashboard')->with('success', __('Account created with Google. Welcome!'));

        } catch (\Exception $e) {
            Log::warning('Google OAuth callback failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'redirect_uri' => config('services.google.redirect'),
                'has_code' => $request->filled('code'),
                'has_state' => $request->filled('state'),
                'oauth_error' => $request->query('error'),
                'oauth_error_description' => $request->query('error_description'),
                'file' => $e->getFile().':'.$e->getLine(),
            ]);

            return redirect()->route('login')->with('error', __('Error connecting with Google: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Redirect to Apple OAuth provider
     */
    public function redirectToApple()
    {
        return Socialite::driver('apple')->redirect();
    }

    /**
     * Handle Apple OAuth callback
     */
    public function handleAppleCallback(Request $request)
    {
        try {
            $appleUser = Socialite::driver('apple')->user();

            // Check if user already exists with this Apple ID
            $user = User::where('apple_id', $appleUser->id)->first();

            if ($user) {
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', __('Signed in with Apple.'));
            }

            // Check if user exists with this email
            $existingUser = User::where('email', $appleUser->email)->first();

            if ($existingUser) {
                // Link Apple account to existing user
                $existingUser->update([
                    'apple_id' => $appleUser->id,
                    'provider' => 'apple',
                ]);

                Auth::login($existingUser);
                return redirect()->route('dashboard')->with('success', __('Apple account linked. Welcome back!'));
            }

            // Create new user (admin role by default — entrepreneur)
            $adminRole = Role::where('nom', Role::ADMIN)->firstOrFail();

            $newUser = User::create([
                'nom' => $appleUser->user['name']['lastName'] ?? '',
                'prenom' => $appleUser->user['name']['firstName'] ?? $appleUser->name ?? __('User'),
                'email' => $appleUser->email,
                'apple_id' => $appleUser->id,
                'provider' => 'apple',
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]);

            Auth::login($newUser);
            return redirect()->route('dashboard')->with('success', __('Account created with Apple. Welcome!'));

        } catch (\Exception $e) {
            Log::warning('Apple OAuth callback failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'redirect_uri' => config('services.apple.redirect'),
                'has_code' => $request->filled('code'),
                'has_state' => $request->filled('state'),
                'oauth_error' => $request->query('error'),
                'oauth_error_description' => $request->query('error_description'),
                'file' => $e->getFile().':'.$e->getLine(),
            ]);

            return redirect()->route('login')->with('error', __('Error connecting with Apple: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Unlink social account
     */
    public function unlinkSocialAccount(Request $request)
    {
        $user = Auth::user();
        $provider = $request->input('provider');
        
        if ($provider === 'google') {
            $user->update([
                'google_id' => null,
                'provider' => $user->apple_id ? 'apple' : null,
                'avatar' => null,
            ]);
        } elseif ($provider === 'apple') {
            $user->update([
                'apple_id' => null,
                'provider' => $user->google_id ? 'google' : null,
            ]);
        }
        
        return back()->with('success', __(':provider account unlinked successfully.', ['provider' => ucfirst($provider)]));
    }
}
