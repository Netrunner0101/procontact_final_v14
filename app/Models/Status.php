<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [
        'status_client',
    ];

    /**
     * Get the contacts for the status.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
