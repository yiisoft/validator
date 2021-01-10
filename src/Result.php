<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

final class Result
{
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(string $message, ?array $params = null): void
    {
        $this->errors[] = new ErrorMessage($message, $params);
    }

    public function getErrors(?TranslatorInterface $translator = null): array
    {
        if ($translator === null) {
            return $this->errors;
        }
        return array_map(function ($error) use ($translator) {
            return $error->getMessage($translator);
        }, $this->errors);
    }
}
