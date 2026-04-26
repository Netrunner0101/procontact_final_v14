@extends('layouts.app')

@section('content')
<div style="max-width: 1024px; margin: 0 auto; padding: 1.5rem;">
    <a href="{{ route('contacts.show', $contact) }}" style="color: #843728; text-decoration: none; font-size: 0.9rem;">
        ← {{ __('Back to contact') }}
    </a>

    <div style="background: #ffffff; border-radius: 0.85rem; padding: 1.75rem; margin-top: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            <div>
                <h1 style="font-family:'Manrope',sans-serif;font-size:1.4rem;margin-bottom:0.25rem;">
                    {{ __('Portal access log') }}
                </h1>
                <p style="color:#75786c;font-size:0.9rem;">
                    {{ $contact->prenom }} {{ $contact->nom }}
                </p>
            </div>
            <form method="POST" action="{{ route('contacts.portal-access.revoke', $contact) }}"
                  onsubmit="return confirm('{{ __('Revoke this client\'s portal access? They will need a new invitation email.') }}');">
                @csrf
                <button type="submit" style="background:#ba1a1a;color:white;padding:0.55rem 1rem;border-radius:0.4rem;border:none;font-weight:600;cursor:pointer;">
                    {{ __('Revoke portal access') }}
                </button>
            </form>
        </div>

        @if(session('success'))
            <div style="background:#c0f0b8;color:#002204;padding:0.7rem 1rem;border-radius:0.5rem;margin:1rem 0;font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <table style="width:100%;border-collapse:collapse;margin-top:1.25rem;font-size:0.88rem;">
            <thead>
                <tr style="background:#f5f3f0;">
                    <th style="text-align:left;padding:0.55rem 0.7rem;border-bottom:1px solid #efecea;">{{ __('When') }}</th>
                    <th style="text-align:left;padding:0.55rem 0.7rem;border-bottom:1px solid #efecea;">{{ __('Event') }}</th>
                    <th style="text-align:left;padding:0.55rem 0.7rem;border-bottom:1px solid #efecea;">{{ __('IP') }}</th>
                    <th style="text-align:left;padding:0.55rem 0.7rem;border-bottom:1px solid #efecea;">{{ __('Details') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="padding:0.5rem 0.7rem;border-bottom:1px solid #efecea;white-space:nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td style="padding:0.5rem 0.7rem;border-bottom:1px solid #efecea;">
                            <code style="background:#fbf9f6;padding:1px 6px;border-radius:3px;font-size:0.82rem;">{{ $log->event }}</code>
                        </td>
                        <td style="padding:0.5rem 0.7rem;border-bottom:1px solid #efecea;color:#75786c;">{{ $log->ip_address ?: '—' }}</td>
                        <td style="padding:0.5rem 0.7rem;border-bottom:1px solid #efecea;color:#75786c;font-size:0.82rem;">
                            @if($log->metadata)
                                {{ json_encode($log->metadata, JSON_UNESCAPED_UNICODE) }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:2rem 0.7rem;text-align:center;color:#75786c;">{{ __('No portal activity yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
