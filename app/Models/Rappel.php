<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rappel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rendez_vous_id',
        'date_rappel',
        'frequence',
    ];

    protected $casts = [
        'date_rappel' => 'datetime',
    ];

    /**
     * Get the appointment that owns the reminder.
     */
    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class);
    }
}
