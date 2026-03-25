<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'user_id',
        'contact_id',
        'activite_id',
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contact for the appointment.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the activity for the appointment.
     */
    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    /**
     * Get the notes for the appointment.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the reminders for the appointment.
     */
    public function rappels(): HasMany
    {
        return $this->hasMany(Rappel::class);
    }

    /**
     * Get the statistics for the appointment.
     */
    public function statistiques(): HasMany
    {
        return $this->hasMany(Statistique::class);
    }

    /**
     * Get the combined date and time as a Carbon instance.
     */
    public function getDateHeureAttribute()
    {
        if ($this->date_debut && $this->heure_debut) {
            return $this->date_debut->format('Y-m-d') . ' ' . $this->heure_debut->format('H:i:s');
        }
        return null;
    }

    /**
     * Get the formatted date and time.
     */
    public function getFormattedDateTimeAttribute()
    {
        if ($this->date_debut && $this->heure_debut) {
            return \Carbon\Carbon::parse($this->date_debut->format('Y-m-d') . ' ' . $this->heure_debut->format('H:i:s'));
        }
        return null;
    }
}
