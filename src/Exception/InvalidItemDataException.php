<?php

namespace App\Exception;

class InvalidItemDataException extends \Exception
{
    public function __construct(string $message = "Invalid item data", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}