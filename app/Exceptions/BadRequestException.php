<?php

namespace App\Exceptions;

class BadRequestException extends ApiException
{
    // Error 400 - Petición incorrecta
    public function __construct(string $message = 'Petición incorrecta', string $errorCode = 'BAD_REQUEST', array $data = [])
    {
        parent::__construct($message, $errorCode, $data, 400);
    }
}