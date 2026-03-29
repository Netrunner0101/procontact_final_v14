<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? __('This link is no longer valid. Please contact your service provider.'));
    }
}
