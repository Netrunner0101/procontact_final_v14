<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPortalToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'contact_id',
        'token_hash',
        'last_used_at',
        'revoked_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null;
    }
}
