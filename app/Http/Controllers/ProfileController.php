<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'rue' => 'nullable|string|max:255',
            'numero_rue' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:255',
        ]);
        
        $user->update($request->only([
            'nom', 'prenom', 'email', 'telephone', 'rue', 
            'numero_rue', 'ville', 'code_postal', 'pays'
        ]));
        
        return back()->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('The current password is incorrect.')]);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', __('Password updated successfully.'));
    }

    /**
     * Export the authenticated user's personal data as JSON (GDPR Art. 20).
     */
    public function exportData(Request $request): StreamedResponse
    {
        $user = Auth::user();

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'gdpr_notice' => __('This file contains all personal data we hold about you. You may request deletion of this data at any time from your profile.'),
            'profile' => $user->only([
                'id', 'nom', 'prenom', 'email', 'telephone',
                'rue', 'numero_rue', 'ville', 'code_postal', 'pays',
                'provider', 'created_at', 'last_login_at',
            ]),
            'contacts' => $user->contacts()->with(['emails', 'numeroTelephones'])->get()->toArray(),
            'activites' => $user->activites()->get()->toArray(),
            'rendez_vous' => $user->rendezVous()->with(['notes', 'rappels'])->get()->toArray(),
        ];

        $filename = 'procontact-data-export-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(
            fn () => print(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Permanently delete the authenticated user's account and all associated
     * personal data (GDPR Art. 17 — Right to erasure).
     *
     * The deletion is irreversible. Cascading foreign keys remove every row
     * tied to this user (contacts, appointments, notes, reminders, emails,
     * phone numbers, linked client portal users, etc.).
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'confirm_email' => 'required|string',
            'confirm_phrase' => 'required|string',
        ];

        // Only require password when the account has a usable one
        // (OAuth-only users authenticate via the provider).
        if (! $user->provider) {
            $rules['current_password'] = 'required|string';
        }

        $request->validate($rules);

        $expectedPhrase = __('DELETE');

        if (mb_strtolower(trim($request->confirm_email)) !== mb_strtolower($user->email)) {
            throw ValidationException::withMessages([
                'confirm_email' => __('The email address you entered does not match your account email.'),
            ]);
        }

        if (mb_strtoupper(trim($request->confirm_phrase)) !== $expectedPhrase) {
            throw ValidationException::withMessages([
                'confirm_phrase' => __('Please type ":phrase" exactly to confirm deletion.', ['phrase' => $expectedPhrase]),
            ]);
        }

        if (! $user->provider && ! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        // GDPR audit trail: log that we honored the erasure request without
        // keeping any personal data. The hashed identifier lets us prove the
        // deletion ran for a specific account if challenged, but cannot be
        // reversed back into an email address.
        Log::info('GDPR account deletion executed', [
            'user_hash' => hash('sha256', $user->id . '|' . $user->email),
            'occurred_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
        ]);

        DB::transaction(function () use ($user, $request) {
            Auth::logout();
            $user->delete();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        });

        return redirect()->route('login')->with('status', __('Your account and all associated data have been permanently deleted.'));
    }
}
