<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Contact extends Model
{
    use HasFactory;

    /**
     * Polymorphic morph children don't get cascaded by SQL foreign keys, so we
     * delete the contact's addresses explicitly when the contact itself is
     * deleted. This enforces the UML composition (Contact ◆── Adresse).
     */
    protected static function booted(): void
    {
        static::deleting(function (self $contact): void {
            $contact->adresses()->delete();
        });
    }

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
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
     * Addresses linked to this contact (polymorphic).
     */
    public function adresses(): MorphMany
    {
        return $this->morphMany(Adresse::class, 'addressable');
    }

    /**
     * Convenience accessor for the contact's primary address.
     */
    public function adressePrincipale(): MorphOne
    {
        return $this->morphOne(Adresse::class, 'addressable')->where('is_principale', true);
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
