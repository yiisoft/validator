<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @psalm-var list<string>
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function isNotValid(): bool
    {
        return $this->isValid() === false;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @psalm-return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
