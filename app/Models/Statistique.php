<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistique extends Model
{
    use HasFactory;

    protected $fillable = [
        'activite_id',
        'rendez_vous_id',
        'contact_id',
    ];

    /**
     * Get the activity for the statistic.
     */
    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    /**
     * Get the appointment for the statistic.
     */
    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class);
    }

    /**
     * Get the contact for the statistic.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
