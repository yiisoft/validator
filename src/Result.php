<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;

final class Result
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

    public function getErrors(
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        string $translationLocale = null
    ): array {
        if ($translator === null) {
            return $this->errors;
        }

        $errors = [];
        foreach ($this->errors as [$message, $parameters]) {
            $errors[] = $translator->translate(
                $message,
                $parameters,
                $translationDomain ?? 'validators',
                $translationLocale
            );
        }

        return $errors;
    }
}
