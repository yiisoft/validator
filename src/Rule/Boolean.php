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
 * values exactly are considered "true" and "false" can be configured via {@see $trueValue} and {@see $falseValue}
 * settings accordingly. There is also an option to choose between strict and non-strict mode of comparison
 * (see {@see $strict}).
 *
 * If the purpose is to check the truthiness only, use {@see IsTrue} rule instead.
 *
 * @see BooleanHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Boolean implements RuleWithOptionsInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
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
     * = Non-strict mode uses `==` operator meaning that type juggling is performed first before the comparison. You can
     * read more in the PHP docs:
     *
     * - https://www.php.net/manual/en/language.operators.comparison.php
     * - https://www.php.net/manual/en/types.comparisons.php
     * - https://www.php.net/manual/en/language.types.type-juggling.php
     *
     * Defaults to `false` meaning non-strict mode is used.
     * @param string $nonScalarMessage Error message used when validation fails and non-scalar value (neither int,
     * float, string nor bool) was provided as input.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{true}` - the value set in {@see $trueValue} option.
     * - `{false}` - the value set in {@see $falseValue} option.
     * - `{type}`: the type of the value being validated.
     * @param string $scalarMessage Error message used when validation fails and scalar value (either int, float, string
     * or bool) was provided as input.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{true}` - the value set in {@see $trueValue} option.
     * - `{false}` - the value set in {@see $falseValue} option.
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
        private string $nonScalarMessage = 'Value must be either "{true}" or "{false}".',
        private string $scalarMessage = 'Value must be either "{true}" or "{false}".',
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
     * A getter for {@see $trueValue} property.
     *
     * @return scalar The value that is considered to be "true".
     */
    public function getTrueValue(): int|float|string|bool
    {
        return $this->trueValue;
    }

    /**
     * A getter for {@see $falseValue} property.
     *
     * @return scalar The value that is considered to be "true".
     */
    public function getFalseValue(): int|float|string|bool
    {
        return $this->falseValue;
    }

    /**
     * A getter for {@see $strict} property.
     *
     * @return bool Whether the comparison to {@see $trueValue} and {@see $falseValue} is strict:
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * A getter for {@see $nonScalarMessage}.
     *
     * @return string Error message.
     */
    public function getNonScalarMessage(): string
    {
        return $this->nonScalarMessage;
    }

    /**
     * A getter for {@see $scalarMessage}.
     *
     * @return string Error message.
     */
    public function getScalarMessage(): string
    {
        return $this->scalarMessage;
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
            'nonScalarMessage' => [
                'template' => $this->nonScalarMessage,
                'parameters' => $messageParameters,
            ],
            'scalarMessage' => [
                'template' => $this->scalarMessage,
                'parameters' => $messageParameters,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return BooleanHandler::class;
    }
}
