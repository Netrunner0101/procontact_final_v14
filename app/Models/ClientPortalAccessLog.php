<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPortalAccessLog extends Model
{
    public $timestamps = false;

    protected $table = 'client_portal_access_log';

    protected $fillable = [
        'contact_id',
        'event',
        'ip_address',
        'user_agent_hash',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
