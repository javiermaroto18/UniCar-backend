<?php

namespace App\Exceptions;

class UnprocessableEntityException extends ApiException
{
    // Error 422 - Entidad no procesable
    public function __construct(string $message = 'Entidad no procesable', string $errorCode = 'UNPROCESSABLE_ENTITY', array $data = [])
    {
        parent::__construct($message, $errorCode, $data, 422);
    }
}