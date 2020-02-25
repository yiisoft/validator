<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Result represents a single value validation result.
 * It may either be success or contain one or multiple errors.
 */
final class RuleResult
{
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(string $message, array $arguments = []): void
    {
        $this->errors[] = [$message, $arguments];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
