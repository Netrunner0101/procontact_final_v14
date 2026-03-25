<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    ];

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
}
