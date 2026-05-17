<?php

namespace App\Exceptions;

class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Recurso no encontrado', string $errorCode = 'NOT_FOUND', array $data = [])
    {
        parent::__construct($message, $errorCode, $data, 404);
    }
}