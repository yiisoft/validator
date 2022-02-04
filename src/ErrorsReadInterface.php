<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ErrorsReadInterface
{
    public function isValid(): bool;

    public function getErrorObjects(): array;

    public function getErrors(): array;

    public function getErrorsIndexedByPath(string $separator = '.'): array;
}
