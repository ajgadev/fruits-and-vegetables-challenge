<?php

namespace App\Exception;

class UnsupportedWeightUnitException extends \Exception
{
    public function __construct(string $message = "Unsupported weight unit", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}