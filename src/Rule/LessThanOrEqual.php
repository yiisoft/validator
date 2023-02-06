<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the specified value is less than or equal to another value or attribute.
 *
 * The value being validated with {@see LessThanOrEqual::$targetValue} or {@see LessThanOrEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see LessThanOrEqual::$type} to
 * {@see LessThanOrEqual::TYPE_NUMBER} to enable numeric validation.
 *
 * `new LessThanOrEqual()` is a shortcut for `new Compare(operator: '<=)`.
 *
 * @see CompareHandler
 * @see AbstractCompare
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class LessThanOrEqual extends AbstractCompare
{
    /**
     * @param scalar|null $targetValue The constant value to be compared with. When both this property and
     * {@see $targetAttribute} are set, this property takes precedence.
     * @param string|null $targetAttribute The name of the attribute to be compared with. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectDataSetTypeMessage A message used when the value returned from a custom
     * data set s not scalar.
     *
     * You may use the following placeholders in the message:
     *
     * - `{type}`: type of the value.
     * @param string|null $message A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{targetValue}`: the constant value to be compared with.
     * - `{targetAttribute}`: the name of the attribute to be compared with.
     * - `{targetValueOrAttribute}`: the constant value to be compared with or, if it's absent, the name of
     *   the attribute to be compared with.
     * - `{value}`: the value of the attribute being validated.
     * @param string $type The type of the values being compared. Either {@see CompareType::STRING}
     * or {@see CompareType::NUMBER}.
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        int|float|string|bool|null $targetValue = null,
        string|null $targetAttribute = null,
        string $incorrectInputMessage = self::DEFAULT_INCORRECT_INPUT_MESSAGE,
        string $incorrectDataSetTypeMessage = self::DEFAULT_INCORRECT_DATA_SET_TYPE_MESSAGE,
        string|null $message = null,
        string $type = CompareType::STRING,
        bool|callable|null $skipOnEmpty = false,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            targetValue: $targetValue,
            targetAttribute: $targetAttribute,
            incorrectInputMessage: $incorrectInputMessage,
            incorrectDataSetTypeMessage: $incorrectDataSetTypeMessage,
            message: $message,
            type: $type,
            operator: '<=',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $skipOnError,
            when: $when,
        );
    }

    public function getName(): string
    {
        return 'lessThanOrEqual';
    }
}
