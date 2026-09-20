<?php

namespace Libraries;

use Libraries\Exceptions\ValidationException;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): static
    {
        return new static($data, $rules);
    }

    public function validate(): void
    {
        foreach ($this->rules as $field => $rules) {

            $value = $this->data[$field] ?? null;

            // Jika nullable dan kosong, skip semua rule lain
            if (
                in_array('nullable', $rules, true) &&
                ($value === null || $value === '')
            ) {
                continue;
            }

            foreach ($rules as $rule) {

                if ($rule === 'nullable') {
                    continue;
                }

                if (is_callable($rule)) {

                    $result = $rule($value, $this->data);

                    if ($result !== true && $result !== null) {
                        $this->errors[$field][] = is_string($result)
                            ? $result
                            : __("Field {$field} not valid.");
                    }

                    continue;
                }

                $this->validateRule(
                    $field,
                    $value,
                    $rule
                );
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }
    }

    protected function validateRule(
        string $field,
        mixed $value,
        string $rule
    ): void {

        [$name, $parameter] = array_pad(
            explode(':', $rule, 2),
            2,
            null
        );

        switch ($name) {

            case 'required':

                if (
                    $value === null ||
                    $value === ''
                ) {
                    $this->errors[$field][] =
                        __("{$field} is required.");
                }

                break;

            case 'string':

                if (
                    $value !== null &&
                    !is_string($value)
                ) {
                    $this->errors[$field][] =
                        __("{$field} must be string.");
                }

                break;

            case 'integer':

                if (
                    $value !== null &&
                    filter_var(
                        $value,
                        FILTER_VALIDATE_INT
                    ) === false
                ) {
                    $this->errors[$field][] =
                        __("{$field} must be integer.");
                }

                break;

            case 'numeric':

                if (
                    $value !== null &&
                    !is_numeric($value)
                ) {
                    $this->errors[$field][] =
                        __("{$field} must be numeric.");
                }

                break;

            case 'email':

                if (
                    $value !== null &&
                    !filter_var(
                        $value,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    $this->errors[$field][] =
                        __("{$field} is invalid.");
                }

                break;

            case 'min':

                if (
                    strlen((string)$value) < (int)$parameter
                ) {
                    $this->errors[$field][] =
                        __("{$field} minimum {$parameter} characters.");
                }

                break;

            case 'max':

                if (
                    strlen((string)$value) > (int)$parameter
                ) {
                    $this->errors[$field][] =
                        __("{$field} maximum {$parameter} characters.");
                }

                break;

            case 'confirmed':

                $confirm =
                    $this->data[$field . '_confirmation'] ?? null;

                if ($confirm !== $value) {
                    $this->errors[$field][] =
                        __("{$field} confirmation mismatch.");
                }

                break;

            case 'in':

                $items = explode(',', $parameter);

                if (!in_array($value, $items, true)) {
                    $this->errors[$field][] =
                        __("{$field} is invalid.");
                }

                break;

            case 'unique':

                [$table, $column, $ignoreId] = array_pad(
                    explode(',', $parameter),
                    3,
                    null
                );

                $query = Database::table($table)
                    ->where($column, $value);

                if ($ignoreId !== null) {
                    $query->where('id', '!=', $ignoreId);
                }

                if ($query->first()) {
                    $this->errors[$field][] =
                        __("{$field} already exists.");
                }

                break;

            case 'exists':

                [$table, $column] = explode(',', $parameter);

                $exists = Database::table($table)
                    ->where($column, $value)
                    ->first();

                if (!$exists) {
                    $this->errors[$field][] =
                        __("{$field} does not exist.");
                }

                break;
        }
    }
}