<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @psalm-var list<int|string, string>
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * @param string $message
     * @param int|string|null $key
     * @return void
     */
    public function addError(string $message, $key = null): void
    {
        if ($key !== null && $key !== 0) {
            $this->errors[$key] = $message;
        } else {
            $this->errors[] = $message;
        }
    }

    /**
     * @psalm-return list<int|string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
