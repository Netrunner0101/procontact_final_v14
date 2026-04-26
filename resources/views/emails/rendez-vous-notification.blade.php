<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Appointment Confirmation') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #843728;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #666;
        }
        .detail-row {
            margin-bottom: 10px;
            padding: 10px;
            background-color: white;
            border-left: 4px solid #843728;
        }
        .detail-label {
            font-weight: bold;
            color: #843728;
        }
        .highlight {
            background-color: #EDE9FE;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Appointment Confirmation') }}</h1>
        <p>Pro Contact</p>
    </div>

    <div class="content">
        <p>{{ __('Hello :first :last,', ['first' => $contact->prenom, 'last' => $contact->nom]) }}</p>

        <p>{{ __('We confirm your appointment:') }}</p>

        <div class="highlight">
            <h2 style="margin-top: 0; color: #843728;">{{ $rendezVous->titre }}</h2>
            @if($rendezVous->description)
                <p><strong>{{ __('Description:') }}</strong> {{ $rendezVous->description }}</p>
            @endif
        </div>

        <div class="detail-row">
            <span class="detail-label">{{ __('Date:') }}</span>
            {{ $rendezVous->date_debut->format('d/m/Y') }}
            @if($rendezVous->date_debut->format('Y-m-d') !== $rendezVous->date_fin->format('Y-m-d'))
                {{ __('to') }} {{ $rendezVous->date_fin->format('d/m/Y') }}
            @endif
        </div>

        <div class="detail-row">
            <span class="detail-label">{{ __('Time:') }}</span>
            {{ $rendezVous->heure_debut->format('H:i') }} - {{ $rendezVous->heure_fin->format('H:i') }}
        </div>

        <div class="detail-row">
            <span class="detail-label">{{ __('Activity:') }}</span>
            {{ $activite->nom }}
        </div>

        @if($activite->description)
            <div class="detail-row">
                <span class="detail-label">{{ __('Activity description:') }}</span>
                {{ $activite->description }}
            </div>
        @endif

        @if($activite->numero_telephone)
            <div class="detail-row">
                <span class="detail-label">{{ __('Phone:') }}</span>
                {{ $activite->numero_telephone }}
            </div>
        @endif

        @if($activite->email)
            <div class="detail-row">
                <span class="detail-label">{{ __('Email:') }}</span>
                {{ $activite->email }}
            </div>
        @endif

        @if($contact->portal_token)
        <div style="margin-top: 20px; padding: 20px; background-color: #EDE9FE; border-radius: 8px; text-align: center;">
            <p style="margin: 0 0 15px 0; font-weight: bold;">{{ __('Access your client portal') }}</p>
            <a href="{{ url('/portal/' . $contact->portal_token) }}" style="display: inline-block; padding: 12px 24px; background-color: #843728; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
                {{ __('View my appointments') }}
            </a>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">{{ __('This link is personal. Do not share it.') }}</p>
        </div>
        @endif

        <div style="margin-top: 20px; padding: 15px; background-color: #FEF3C7; border-radius: 5px;">
            <p style="margin: 0;"><strong>{{ __('Important:') }}</strong> {{ __('Please confirm your attendance or let us know if you are unable to attend.') }}</p>
        </div>

        <p>{{ __('Thank you for your trust. We remain at your disposal for any questions.') }}</p>

        <p>{{ __('Best regards,') }}<br>
        {{ __('The Pro Contact Team') }}</p>
    </div>

    <div class="footer">
        <p>{{ __('This email was sent automatically from Pro Contact.') }}</p>
        <p>{!! __('For any question, email us at <a href="mailto:contact@procontact.app">contact@procontact.app</a>.') !!}</p>
    </div>
</body>
</html>
