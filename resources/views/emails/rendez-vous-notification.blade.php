<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmation de rendez-vous</title>
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
            background-color: #8B5CF6;
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
            border-left: 4px solid #8B5CF6;
        }
        .detail-label {
            font-weight: bold;
            color: #8B5CF6;
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
        <h1>Confirmation de Rendez-vous</h1>
        <p>Pro Contact</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $contact->prenom }} {{ $contact->nom }},</p>
        
        <p>Nous vous confirmons votre rendez-vous :</p>

        <div class="highlight">
            <h2 style="margin-top: 0; color: #8B5CF6;">{{ $rendezVous->titre }}</h2>
            @if($rendezVous->description)
                <p><strong>Description :</strong> {{ $rendezVous->description }}</p>
            @endif
        </div>

        <div class="detail-row">
            <span class="detail-label">Date :</span>
            {{ $rendezVous->date_debut->format('d/m/Y') }}
            @if($rendezVous->date_debut->format('Y-m-d') !== $rendezVous->date_fin->format('Y-m-d'))
                au {{ $rendezVous->date_fin->format('d/m/Y') }}
            @endif
        </div>

        <div class="detail-row">
            <span class="detail-label">Heure :</span>
            {{ $rendezVous->heure_debut->format('H:i') }} - {{ $rendezVous->heure_fin->format('H:i') }}
        </div>

        <div class="detail-row">
            <span class="detail-label">Activité :</span>
            {{ $activite->nom }}
        </div>

        @if($activite->description)
            <div class="detail-row">
                <span class="detail-label">Description de l'activité :</span>
                {{ $activite->description }}
            </div>
        @endif

        @if($activite->numero_telephone)
            <div class="detail-row">
                <span class="detail-label">Téléphone :</span>
                {{ $activite->numero_telephone }}
            </div>
        @endif

        @if($activite->email)
            <div class="detail-row">
                <span class="detail-label">Email :</span>
                {{ $activite->email }}
            </div>
        @endif

        <div style="margin-top: 20px; padding: 15px; background-color: #FEF3C7; border-radius: 5px;">
            <p style="margin: 0;"><strong>Important :</strong> Merci de confirmer votre présence ou de nous prévenir en cas d'empêchement.</p>
        </div>

        <p>Nous vous remercions de votre confiance et restons à votre disposition pour toute question.</p>

        <p>Cordialement,<br>
        L'équipe Pro Contact</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement depuis Pro Contact.</p>
        <p>Merci de ne pas répondre directement à cet email.</p>
    </div>
</body>
</html>
