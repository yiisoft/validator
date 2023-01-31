<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Contains a set of options to determine if the value is not empty according to {@see $emptyCriteria}. When rule-level
 * criteria is not set, a handler-level criteria ({@see RequiredHandler::$defaultEmptyCriteria}) is applied (which is
 * also customizable). In case of using attributes, the attribute must be present with passed non-empty value.
 *
 * With default settings in order for value to pass the validation it must satisfy all the following conditions:
 *
 * - Passed.
 * - Not `null`.
 * - Not an empty string (after trimming).
 * - Not an empty array.
 *
 * When using with other rules, it must come first.
 *
 * @see RequiredHandler Corresponding handler performing the actual validation.
 *
 * @psalm-type EmptyCriteriaType = callable(mixed,bool):bool
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Required implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var callable|null An empty criteria (either a callable or class implementing `__invoke()` method) used to
     * determine emptiness of the value. The signature must be like the following:
     *
     * ```php
     * function (mixed $value, bool $isAttributeMissing): bool
     * ```
     *
     * `$isAttributeMissing` is a flag defining whether the attribute is missing (not used / not passed at all).
     *
     * @psalm-var EmptyCriteriaType|null
     */
    private $emptyCondition;

    /**
     * @param string $message Error message used when validation fails because the validated value is empty.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * @param string $notPassedMessage Error message used when validation fails because the validated value is not
     * passed.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * @param callable|null $emptyCriteria An empty criteria used to determine emptiness of the value.
     * @psalm-param EmptyCriteriaType|null $emptyCriteria
     *
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private string $message = 'Value cannot be blank.',
        private string $notPassedMessage = 'Value not passed.',
        callable|null $emptyCriteria = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->emptyCondition = $emptyCriteria;
    }

    public function getName(): string
    {
        return 'required';
    }

    /**
     * Gets error message used when validation fails because the validated value is empty.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Gets error message used when validation fails because the validated value is not passed.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getNotPassedMessage(): string
    {
        return $this->notPassedMessage;
    }

    /**
     * Gets empty criteria used to determine emptiness of the value.
     *
     * @return callable|null Empty criteria.
     * @psalm-return EmptyCriteriaType|null
     *
     * @see $emptyCondition
     */
    public function getEmptyCondition(): ?callable
    {
        return $this->emptyCondition;
    }

    public function getOptions(): array
    {
        return [
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'notPassedMessage' => [
                'template' => $this->notPassedMessage,
                'parameters' => [],
            ],
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return RequiredHandler::class;
    }
}
