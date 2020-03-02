<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule
{
    private ?TranslatorInterface $translator = null;
    private ?string $translationDomain = null;
    private ?string $translationLocale = null;
    private bool $skipOnEmpty = false;
    private bool $skipOnError = true;

    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @param bool $previousRulesErrored set to true if rule is part of a group of rules and one of the previous validations failed
     * @return Result
     */
    final public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if ($this->skipOnError && $previousRulesErrored) {
            return new Result();
        }

        return $this->validateValue($value, $dataSet);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @return Result
     */
    abstract protected function validateValue($value, DataSetInterface $dataSet = null): Result;

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

    public function translateMessage(string $message, array $arguments = []): string
    {
        if ($this->translator === null) {
            return $this->formatMessage($message, $arguments);
        }

        return $this->translator->translate(
            $message,
            $arguments,
            $this->translationDomain ?? 'validators',
            $this->translationLocale
        );
    }

    public function skipOnError(bool $value): self
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     * @return self
     */
    public function skipOnEmpty(bool $value): self
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
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

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value the value to be checked
     * @return bool whether the value is empty
     */
    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
