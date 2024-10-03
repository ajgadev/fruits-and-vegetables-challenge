<?php

namespace App\Exception;

class UnsupportedFoodTypeException extends \Exception
{
    public function __construct(string $message = "Unsupported food type", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}