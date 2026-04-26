<?php

namespace App\Http\Controllers;

use App\Models\ClientPortalAccessLog;
use App\Models\Contact;
use App\Services\PortalAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPortalAccessController extends Controller
{
    public function __construct(private PortalAuthService $authService) {}

    public function show(Contact $contact)
    {
        abort_unless($contact->user_id === Auth::id(), 403);

        $logs = ClientPortalAccessLog::where('contact_id', $contact->id)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('admin.portal-access', [
            'contact' => $contact,
            'logs' => $logs,
        ]);
    }

    public function revoke(Request $request, Contact $contact)
    {
        abort_unless($contact->user_id === Auth::id(), 403);

        $this->authService->revokeAllTokens($contact);
        $this->authService->revokeAllTrustedDevices($contact);
        $this->authService->log($contact->id, 'access_revoked_by_admin', $request, [
            'admin_user_id' => Auth::id(),
        ]);

        return back()->with('success', __('Portal access revoked. The client will need a new invitation.'));
    }
}
