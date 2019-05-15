<?php


namespace Yii\Validator;

/**
 * Result represents validation result
 */
final class Result
{
    private $valid = true;
    private $errors = [];

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
