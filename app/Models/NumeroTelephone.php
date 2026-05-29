<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumeroTelephone extends Model
{
    use HasFactory;

    protected $table = 'numero_telephones';

    protected $fillable = [
        'user_id',
        'contact_id',
        'numero_telephone',
        'pays_code',
    ];

    /**
     * Get the contact that owns the phone number.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Country providing the phone prefix (indicatif) for this number.
     */
    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'pays_code', 'code');
    }

    /**
     * Full international number = indicatif + national number.
     *
     * Falls back to the stored value when no country is set (legacy rows that
     * may already embed the prefix inside `numero_telephone`).
     */
    public function getFullNumberAttribute(): string
    {
        if ($this->pays_code && $this->pays) {
            return trim($this->pays->indicatif.' '.$this->numero_telephone);
        }

        return (string) $this->numero_telephone;
    }

    /**
     * Get the user that owns the phone number (when not attached to a contact).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
