<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

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
     * @var callable|null
     */
    private $when = null;

    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param ValidationContext|null $context optional validation context
     *
     * @return Result
     */
    final public function validate($value, ValidationContext $context = null): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if (
            ($this->skipOnError && $context && $context->isPreviousRulesErrored()) ||
            (is_callable($this->when) && !($this->when)($value, $context))
        ) {
            return new Result();
        }

        return $this->validateValue($value, $context);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value value to be validated
     * @param ValidationContext|null $context optional validation context
     *
     * @return Result
     */
    abstract protected function validateValue($value, ValidationContext $context = null): Result;

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

    /**
     * Add a PHP callable whose return value determines whether this rule should be applied.
     * By default rule will be always applied.
     *
     * The signature of the callable should be `function ($value, ValidationContext $context): bool`,
     * where `$value` and `$context` refer to the value validated and the validation context.
     * The callable should return a boolean value.
     *
     * The following example will enable the validator only when the country currently selected is USA:
     *
     * ```php
     * function ($value, DataSetInterface $dataSet) {
     * return $dataSet->getAttributeValue('country') === Country::USA;
     * }
     * ```
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function when(callable $callback): self
    {
        $new = clone $this;
        $new->when = $callback;
        return $new;
    }

    public function skipOnError(bool $value): self
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     *
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
     *
     * @param mixed $value the value to be checked
     *
     * @return bool whether the value is empty
     */
    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }

    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default it returns base name of the class, first letter in lowercase.
     *
     * @return string
     */
    public function getName(): string
    {
        $className = static::class;
        return lcfirst(substr($className, strrpos($className, '\\') + 1));
    }

    /**
     * Returns rule options as array.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
