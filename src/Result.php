<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @psalm-var array<int|string, string>
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * Add an error.
     *
     * @param string $message Error message.
     * @param int|string|null $key For simple rules the key is null meaning error will be appended to the end of the
     * array. Otherwise, it's a path to a current error value in the input data concatenated using dot notation. For
     * example: "charts.0.points.0.coordinates.x".
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
     * @psalm-return array<int|string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
