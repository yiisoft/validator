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
 * A variation of {@see Boolean} rule limiting the allowed values to "true" only (not limited to boolean "true" type).
 * What value exactly is considered "true" can be configured via {@see $trueValue} setting. There is also an option to
 * choose between strict and non-strict mode of comparison (see {@see $strict}).
 *
 * A typical scope of application is a user agreement. If the purpose is to also check the falsiness, use {@see Boolean}
 * rule instead.
 *
 * @see IsTrueHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class IsTrue implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @const Default message used for all cases.
     */
    private const DEFAULT_MESSAGE = 'The value must be "{true}".';

    /**
     * @param scalar $trueValue The value that is considered to be "true". Only scalar values (either int, float, string
     * or bool) are allowed. Defaults to `1` string.
     * @param bool $strict Whether the comparison to {@see $trueValue} is strict:
     *
     * - Strict mode uses `===` operator meaning the type and the value must both match to those set in
     * {@see $trueValue}.
     * - Non-strict mode uses `==` operator meaning that type juggling is performed first before the comparison. You can
     * read more in the PHP docs:
     *
     * - https://www.php.net/manual/en/language.operators.comparison.php
     * - https://www.php.net/manual/en/types.comparisons.php
     * - https://www.php.net/manual/en/language.types.type-juggling.php
     *
     * Defaults to `false` meaning non-strict mode is used.
     * @param string $messageWithType Error message used when validation fails and neither non-scalar value (int,
     * float, string, bool) nor null was provided as input. The type is used instead of value here for more predictable
     * formatting.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{true}` - the value set in {@see $trueValue} option.
     * - `{type}`: the type of the value being validated.
     * @param string $messageWithValue Error message used when validation fails and either scalar value (int, float,
     * string, bool) or null was provided as input.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{true}` - the value set in {@see $trueValue} option.
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
        private bool $strict = false,
        private string $messageWithType = self::DEFAULT_MESSAGE,
        private string $messageWithValue = self::DEFAULT_MESSAGE,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'isTrue';
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
     * A getter for {@see $strict} property.
     *
     * @return bool Whether the comparison to {@see $trueValue} is strict:
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * A getter for {@see $messageWithType}.
     *
     * @return string Error message.
     */
    public function getMessageWithType(): string
    {
        return $this->messageWithType;
    }

    /**
     * A getter for {@see $messageWithValue}.
     *
     * @return string Error message.
     */
    public function getMessageWithValue(): string
    {
        return $this->messageWithValue;
    }

    public function getOptions(): array
    {
        $messageParameters = [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
        ];

        return [
            'trueValue' => $this->trueValue,
            'strict' => $this->strict,
            'messageWithType' => [
                'template' => $this->messageWithType,
                'parameters' => $messageParameters,
            ],
            'messageWithValue' => [
                'template' => $this->messageWithValue,
                'parameters' => $messageParameters,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return IsTrueHandler::class;
    }
}
