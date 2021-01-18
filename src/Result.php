<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @var Message[]
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(Message $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @return Message[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
