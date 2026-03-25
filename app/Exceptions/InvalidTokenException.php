<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    public function __construct(string $message = 'Ce lien n\'est plus valide. Veuillez contacter votre prestataire.')
    {
        parent::__construct($message);
    }
}
