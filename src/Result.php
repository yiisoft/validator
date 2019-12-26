<?php


namespace Yiisoft\Validator;

/**
 * Result represents a single value validation result.
 * It may either be success or contain one or multiple errors.
 */
final class Result
{
    private bool $valid = true;
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function addError(string $message): void
    {
        $this->valid = false;
        $this->errors[] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
