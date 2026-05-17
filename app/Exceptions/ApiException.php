<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    protected array $data;

    public function __construct(string $message = '', string $errorCode = 'ERROR', array $data = [], int $statusCode = 500)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int { return $this->statusCode; }
    public function getErrorCode(): string { return $this->errorCode; }
    public function getData(): array { return $this->data; }
}
