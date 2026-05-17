<?php

namespace App\Exceptions;

class ForbiddenException extends ApiException
{
    // Error 403 - Acceso denegado
    public function __construct(string $message = 'Acceso denegado', string $errorCode = 'FORBIDDEN', array $data = [])
    {
        parent::__construct($message, $errorCode, $data, 403);
    }
}