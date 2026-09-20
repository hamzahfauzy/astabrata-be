<?php

namespace Libraries\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected array $errors = [];

    public function __construct(array $errors)
    {
        parent::__construct('Not Found');

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}