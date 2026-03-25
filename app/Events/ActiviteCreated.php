<?php

namespace App\Events;

use App\Models\Activite;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiviteCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Activite $activite)
    {
    }
}
