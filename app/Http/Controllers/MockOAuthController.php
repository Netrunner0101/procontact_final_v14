<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MockOAuthController extends Controller
{
    /**
     * Mock Google OAuth for testing purposes
     */
    public function mockGoogleAuth(Request $request)
    {
        // Create or find a test user
        $email = $request->input('email', 'test@gmail.com');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Create a new user with Google-like data
            $user = User::create([
                'nom' => 'Test',
                'prenom' => 'User',
                'email' => $email,
                'google_id' => 'mock_google_id_' . Str::random(10),
                'provider' => 'google',
                'avatar' => 'https://via.placeholder.com/150/4285F4/FFFFFF?text=G',
                'password' => Hash::make(Str::random(16)),
                'role_id' => 1, // admin
            ]);
        } else {
            // Update existing user with Google info
            $user->update([
                'google_id' => 'mock_google_id_' . Str::random(10),
                'provider' => 'google',
                'avatar' => 'https://via.placeholder.com/150/4285F4/FFFFFF?text=G',
            ]);
        }
        
        // Log the user in
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Connexion réussie avec Google (mode test)!');
    }
    
    /**
     * Show mock OAuth selection page
     */
    public function showMockAuth()
    {
        return view('mock-oauth');
    }
}
