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
        'contact_id',
        'numero_telephone',
    ];

    /**
     * Get the contact that owns the phone number.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
