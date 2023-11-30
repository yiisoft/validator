<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is an integer number.
 *
 * The format of the number must match the regular expression specified in {@see Integer::$pattern}. Optionally, you may
 * configure the {@see Integer::$min} and {@see Integer::$max} to ensure the number is within a certain range.
 *
 * @see NumberHandler
 * @see AbstractNumber
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Integer extends AbstractNumber
{
    /**
     * @param float|int|null $min Lower limit of the number. Defaults to `null`, meaning no lower limit. See
     * {@see $lessThanMinMessage} for the customized message used when the number is too small.
     * @param float|int|null $max Upper limit of the number. Defaults to `null`, meaning no upper limit. See
     * {@see $greaterThanMaxMessage} for the customized message used when the number is too big.
     * @param string $incorrectInputMessage Error message used when the value is not numeric.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $notNumberMessage Error message used when the value does not match {@see $pattern}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{value}`: actual value.
     * @param string $lessThanMinMessage Error message used when the value is smaller than {@link $min}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{min}`: minimum value.
     * - `{value}`: actual value.
     * @param string $greaterThanMaxMessage Error message used when the value is bigger than {@link $max}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{max}`: maximum value.
     * - `{value}`: actual value.
     * @param string $pattern The regular expression for matching numbers. It defaults to a pattern that matches integer
     * numbers with optional leading zero part (e.g. 01).
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty. See
     * {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param WhenType $when
     */
    public function __construct(
        float|int|null $min = null,
        float|int|null $max = null,
        string $incorrectInputMessage = self::DEFAULT_INCORRECT_INPUT_MESSAGE,
        string $notNumberMessage = 'Value must be an integer.',
        string $lessThanMinMessage = self::DEFAULT_LESS_THAN_MIN_MESSAGE,
        string $greaterThanMaxMessage = self::DEFAULT_GREATER_THAN_MAX_MESSAGE,
        string $pattern = '/^\s*[+-]?\d+\s*$/',
        mixed $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            min: $min,
            max: $max,
            incorrectInputMessage: $incorrectInputMessage,
            notNumberMessage: $notNumberMessage,
            lessThanMinMessage: $lessThanMinMessage,
            greaterThanMaxMessage: $greaterThanMaxMessage,
            pattern: $pattern,
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $skipOnError,
            when: $when,
        );
    }
}
