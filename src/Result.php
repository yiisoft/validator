<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    public const ERROR_KEY_SEPARATOR = '.';

    /**
     * @var array<mixed, string>
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(string $message, $key = null): void
    {
        if ($key) {
            $this->errors[$key] = $message;
        } else {
            $this->errors[] = $message;
        }
    }

    /**
     * @return array<mixed, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
