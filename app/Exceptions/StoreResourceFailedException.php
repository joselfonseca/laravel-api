<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class StoreResourceFailedException extends Exception
{
    public $errors = [];

    public function __construct($message = '', $errors = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }
}
