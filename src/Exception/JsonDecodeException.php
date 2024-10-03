<?php

namespace App\Exception;

class JsonDecodeException extends \Exception
{
    public function __construct(string $message = "Failed to decode JSON", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}