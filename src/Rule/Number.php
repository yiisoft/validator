<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$pattern}. Optionally, you may
 * configure the {@see Number::$min} and {@see Number::$max} to ensure the number is within a certain range.
 *
 * @see NumberHandler
 * @see AbstractNumber
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Number extends AbstractNumber
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
     * @param string $pattern The regular expression for matching numbers. It defaults to a pattern that matches
     * floating numbers with optional exponential part (e.g. -1.23e-10).
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
        string $incorrectInputMessage = 'The allowed types are integer, float and string.',
        string $notNumberMessage = 'Value must be a number.',
        string $lessThanMinMessage = 'Value must be no less than {min}.',
        string $greaterThanMaxMessage = 'Value must be no greater than {max}.',
        string $pattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
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

    public function getName(): string
    {
        return self::class;
    }
}
