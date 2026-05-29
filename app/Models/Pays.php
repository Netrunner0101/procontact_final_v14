<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pays extends Model
{
    protected $table = 'pays';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['code', 'nom', 'indicatif'];

    public function adresses(): HasMany
    {
        return $this->hasMany(Adresse::class, 'pays_code', 'code');
    }

    /**
     * Phone numbers using this country's calling code (indicatif).
     */
    public function numerosTelephone(): HasMany
    {
        return $this->hasMany(NumeroTelephone::class, 'pays_code', 'code');
    }
}
