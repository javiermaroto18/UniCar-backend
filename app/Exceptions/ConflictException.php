<?php

namespace App\Exceptions;

class ConflictException extends ApiException
{
    // Error 409 - Conflicto en la petición
    public function __construct(string $message = 'Conflicto en la petición', string $errorCode = 'CONFLICT', array $data = [])
    {
        parent::__construct($message, $errorCode, $data, 409);
    }
}