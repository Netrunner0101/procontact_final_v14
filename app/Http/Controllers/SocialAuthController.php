<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    public function handleGoogleCallback()
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
                return redirect()->route('dashboard');
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
                return redirect()->route('dashboard');
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
            ]);

            $newUser->role_id = $adminRole->id;
            $newUser->save();

            Auth::login($newUser);
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
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
    public function handleAppleCallback()
    {
        try {
            $appleUser = Socialite::driver('apple')->user();
            
            // Check if user already exists with this Apple ID
            $user = User::where('apple_id', $appleUser->id)->first();
            
            if ($user) {
                Auth::login($user);
                return redirect()->route('dashboard');
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
                return redirect()->route('dashboard');
            }
            
            // Create new user (admin role by default — entrepreneur)
            $adminRole = Role::where('nom', Role::ADMIN)->firstOrFail();

            $newUser = User::create([
                'nom' => $appleUser->user['name']['lastName'] ?? '',
                'prenom' => $appleUser->user['name']['firstName'] ?? $appleUser->name ?? 'Utilisateur',
                'email' => $appleUser->email,
                'apple_id' => $appleUser->id,
                'provider' => 'apple',
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => now(),
            ]);

            $newUser->role_id = $adminRole->id;
            $newUser->save();

            Auth::login($newUser);
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Apple: ' . $e->getMessage());
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
        
        return back()->with('success', 'Compte ' . ucfirst($provider) . ' dissocié avec succès.');
    }
}
