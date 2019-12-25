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

    public function addError(string $message): self
    {
        $new = clone $this;
        $new->valid = false;
        $new->errors[] = $message;
        return $new;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
