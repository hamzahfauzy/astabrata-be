<?php

namespace Libraries\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected array $errors = [];

    public function __construct(array $errors)
    {
        parent::__construct('Validation Error');

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}