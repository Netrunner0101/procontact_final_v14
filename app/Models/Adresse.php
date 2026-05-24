<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'adresses';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'rue',
        'numero_rue',
        'code_postal',
        'ville',
        'pays_code',
        'is_principale',
    ];

    protected $casts = [
        'is_principale' => 'boolean',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'pays_code', 'code');
    }

    public function isEmpty(): bool
    {
        return blank($this->rue) && blank($this->numero_rue)
            && blank($this->code_postal) && blank($this->ville)
            && blank($this->pays_code);
    }
}
