<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'rue',
        'numero',
        'ville',
        'code_postal',
        'pays',
        'state_client',
        'status_id',
        'portal_token',
        'gdpr_consent_token',
        'gdpr_consent_requested_at',
        'gdpr_consent_signed_at',
        'gdpr_consent_declined_at',
        'gdpr_consent_version',
        'gdpr_consent_ip',
    ];

    protected $casts = [
        'gdpr_consent_requested_at' => 'datetime',
        'gdpr_consent_signed_at' => 'datetime',
        'gdpr_consent_declined_at' => 'datetime',
    ];

    public function hasSignedGdprConsent(): bool
    {
        return $this->gdpr_consent_signed_at !== null;
    }

    public function hasDeclinedGdprConsent(): bool
    {
        return $this->gdpr_consent_declined_at !== null;
    }

    public function isGdprConsentPending(): bool
    {
        return $this->gdpr_consent_requested_at !== null
            && $this->gdpr_consent_signed_at === null
            && $this->gdpr_consent_declined_at === null;
    }

    /**
     * Get the user that owns the contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the contact.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the appointments for the contact.
     */
    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    /**
     * Get the emails for the contact.
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Get the phone numbers for the contact.
     */
    public function numeroTelephones(): HasMany
    {
        return $this->hasMany(NumeroTelephone::class);
    }

    /**
     * The activities that belong to the contact.
     */
    public function activites(): BelongsToMany
    {
        return $this->belongsToMany(Activite::class, 'contact_activite');
    }

    /**
     * Check if the contact has any appointments (making them a "Client").
     * Contact with no appointments = "Contact" status.
     */
    public function isClient(): bool
    {
        return $this->rendezVous()->exists();
    }
}
