<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;

final class Result
{
    private ?TranslatorInterface $translator = null;
    private ?string $translationDomain = null;
    private ?string $translationLocale = null;

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

    public function translator(TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;
        return $new;
    }

    public function translationDomain(string $translation): self
    {
        $new = clone $this;
        $new->translationDomain = $translation;
        return $new;
    }

    public function translationLocale(string $locale): self
    {
        $new = clone $this;
        $new->translationLocale = $locale;
        return $new;
    }

    protected function translateMessage(string $message, array $parameters = []): string
    {
        if ($this->translator === null) {
            return $this->formatMessage($message, $parameters);
        }

        return $this->translator->translate(
            $message,
            $parameters,
            $this->translationDomain ?? 'validators',
            $this->translationLocale
        );
    }

    private function formatMessage(string $message, array $arguments = []): string
    {
        $replacements = [];
        foreach ($arguments as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($message, $replacements);
    }
}
