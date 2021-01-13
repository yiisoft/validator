<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @var ErrorMessage[]
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(ErrorMessage $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return ErrorMessage[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
