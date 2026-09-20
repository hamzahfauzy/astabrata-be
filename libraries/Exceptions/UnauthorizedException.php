<?php

namespace Libraries\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected array $errors = [];

    public function __construct($errors)
    {
        parent::__construct('Unauthorized');

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}