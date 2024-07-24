<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the specified value is equal to "target" value provided directly
 * ({@see GreaterThan::$targetValue}) or within an property ({@see GreaterThan::$targetProperty}).
 *
 * The default comparison is based on number values (including float values). It's also possible to compare values as
 * strings byte by byte and compare original values as is. See {@see NotEqual::$type} for all possible options.
 *
 * - `new GreaterThan()` is a shortcut for `new Compare(operator: '>')`.
 *
 * @see CompareHandler
 * @see AbstractCompare
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class GreaterThan extends AbstractCompare
{
    /**
     * @param mixed $targetValue The value to be greater than. When both this property and {@see $targetProperty} are
     * set, this property takes precedence.
     * @param string|null $targetProperty The property to be greater than. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
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
     * - `{property}`: the translated label of the property being validated.
     * - `{targetValue}`: the value to be compared with.
     * - `{targetProperty}`: the name of the property to be compared with.
     * - `{targetPropertyValue}`: the value extracted from the property to be compared with if this attribute was set.
     * - `{targetValueOrProperty}`: the value to be compared with or, if it's absent, the name of the property to be
     * compared with.
     * - `{value}`: the value being validated.
     *
     * When {@see CompareType::ORIGINAL} is used with complex types (neither scalar nor `null`), `{targetValue}`,
     * `{targetPropertyValue}` and `{targetValueOrProperty}` parameters might contain the actual type instead of the
     * value, e.g. "object" for predictable formatting.
     * @param string $type The type of the values being compared:
     *
     * - {@see CompareType::NUMBER}: default, both values will be converted to float numbers before comparison.
     * - {@see CompareType::ORIGINAL} - compare the values as is.
     * - {@see CompareType::STRING} - cast both values to strings before comparison.
     *
     * {@see CompareType::NUMBER} and {@see CompareType::STRING} allow only scalar and `null` values, also objects
     * implementing {@see Stringable} interface.
     *
     * {@see CompareType::ORIGINAL} allows any values. All PHP comparison rules apply here, see comparison operators -
     * {@see https://www.php.net/manual/en/language.operators.comparison.php} and PHP type comparison tables -
     * {@see https://www.php.net/manual/en/types.comparisons.php} sections in official PHP documentation.
     *
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when  A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        mixed $targetValue = null,
        string|null $targetProperty = null,
        string $incorrectInputMessage = self::DEFAULT_INCORRECT_INPUT_MESSAGE,
        string $incorrectDataSetTypeMessage = self::DEFAULT_INCORRECT_DATA_SET_TYPE_MESSAGE,
        string|null $message = null,
        string $type = CompareType::NUMBER,
        bool|callable|null $skipOnEmpty = false,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            targetValue: $targetValue,
            targetProperty: $targetProperty,
            incorrectInputMessage: $incorrectInputMessage,
            incorrectDataSetTypeMessage: $incorrectDataSetTypeMessage,
            message: $message,
            type: $type,
            operator: '>',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $skipOnError,
            when: $when,
        );
    }
}
