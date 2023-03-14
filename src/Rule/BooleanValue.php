<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Contains a set of options to determine if the value is "true" or "false", not limited to boolean type only. What
 * values exactly are considered "true" and "false" can be configured via {@see BooleanValue::$trueValue} and
 * {@see BooleanValue::$falseValue} settings accordingly. There is also an option to choose between strict and
 * non-strict mode of comparison (see {@see BooleanValue::$strict}).
 *
 * If the purpose is to check the truthiness only, use {@see TrueValue} rule instead.
 *
 * @see BooleanValueHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class BooleanValue implements RuleWithOptionsInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param scalar $trueValue The value that is considered to be "true". Only scalar values (either int, float, string
     * or bool) are allowed. Defaults to `1` string.
     * @param scalar $falseValue The value that is considered to be "false". Only scalar values (either int, float,
     * string or bool) are allowed. Defaults to `0` string.
     * @param bool $strict Whether the comparison to {@see $trueValue} and {@see $falseValue} is strict:
     *
     * - Strict mode uses `===` operator meaning the type and the value must both match to those set in
     * {@see $trueValue} or {@see $falseValue}.
     * - Non-strict mode uses `==` operator meaning that type juggling is performed first before the comparison. You can
     * read more in the PHP docs:
     *
     * - {@link https://www.php.net/manual/en/language.operators.comparison.php}
     * - {@link https://www.php.net/manual/en/types.comparisons.php}
     * - {@link https://www.php.net/manual/en/language.types.type-juggling.php}
     *
     * Defaults to `false` meaning non-strict mode is used.
     * @param string $incorrectInputMessage Error message used when validation fails because the type of validated value
     * is incorrect. Only scalar values are allowed - either int, float, string or bool. Used for more predictable
     * formatting.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{true}`: the value set in {@see $trueValue} option.
     * - `{false}`: the value set in {@see $falseValue} option.
     * - `{type}`: the type of the value being validated.
     * @param string $message Error message used when validation fails because the validated value does not match
     * neither "true" nor "false" values.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{true}`: the value set in {@see $trueValue} option.
     * - `{false}`: the value set in {@see $falseValue} option.
     * - `{value}`: the value being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty / not passed.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private int|float|string|bool $trueValue = '1',
        private int|float|string|bool $falseValue = '0',
        private bool $strict = false,
        private string $incorrectInputMessage = 'The allowed types are integer, float, string, boolean. {type} given.',
        private string $message = 'Value must be either "{true}" or "{false}".',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'boolean';
    }

    /**
     * Gets the value that is considered to be "true".
     *
     * @return scalar A scalar value.
     *
     * @see $trueValue
     */
    public function getTrueValue(): int|float|string|bool
    {
        return $this->trueValue;
    }

    /**
     * Gets the value that is considered to be "false".
     *
     * @return scalar A scalar value.
     *
     * @see $falseValue
     */
    public function getFalseValue(): int|float|string|bool
    {
        return $this->falseValue;
    }

    /**
     * Whether the comparison to {@see $trueValue} and {@see $falseValue} is strict.
     *
     * @return bool `true` - strict, `false` - non-strict.
     *
     * @see $strict
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Gets error message used when validation fails because the type of validated value is incorrect.
     *
     * @return string Error message / template.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Gets error message used when validation fails because the validated value does not match neither "true" nor
     * "false" values.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        $messageParameters = [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
            'false' => $this->falseValue === false ? 'false' : $this->falseValue,
        ];

        return [
            'trueValue' => $this->trueValue,
            'falseValue' => $this->falseValue,
            'strict' => $this->strict,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => $messageParameters,
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => $messageParameters,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return BooleanValueHandler::class;
    }
}
