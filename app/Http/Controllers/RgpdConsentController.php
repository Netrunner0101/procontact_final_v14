<?php

namespace App\Http\Controllers;

use App\Mail\RgpdConsentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RgpdConsentController extends Controller
{
    public function show()
    {
        return view('rgpd.consent', [
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => __('You must accept the Privacy Policy and Terms of Service to continue using your account.'),
        ]);

        $user = $request->user();
        $version = config('app.rgpd_version', '2026-05');

        $user->forceFill([
            'terms_accepted_at' => now(),
            'terms_accepted_version' => $version,
        ])->save();

        Log::channel('rgpd')->info('GDPR consent recorded (existing user reminder)', [
            'user_id' => $user->id,
            'email' => $user->email,
            'consent' => true,
            'version' => $version,
            'accepted_at' => $user->terms_accepted_at->toIso8601String(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        try {
            Mail::to($user->email)->send(new RgpdConsentMail($user, true, $version));
        } catch (\Exception $e) {
            Log::error('Failed to send GDPR consent email: '.$e->getMessage());
        }

        $target = $user->isClient() ? route('client.dashboard') : route('dashboard');

        return redirect()->intended($target)->with('success', __('Thank you. Your consent has been recorded and a confirmation email is on its way.'));
    }
}
