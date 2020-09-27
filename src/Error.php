<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Error implements TranslatableErrorInterface
{
    use TranslatableTrait;

    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * @param string $message
     * @param array $parameters
     */
    public function addError(string $message, array $parameters = []): void
    {
        $this->errors[] = [$message, $parameters];
    }

    public function getErrors(): array
    {
        return array_map(function ($error) {
            [$message, $parameters] = $error;
            return $this->translateMessage($message, $parameters);
        }, $this->errors);
    }

    public function getRawErrors()
    {
        return $this->errors;
    }
}
