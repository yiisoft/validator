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
 * Defines validation options to check that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 *
 * @see NumberHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Number implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param bool $integerOnly Whether the value can only be an integer. Defaults to `false`.
     * @param float|int|null $min Lower limit of the number. Defaults to `null`, meaning no lower limit.
     * See {@see $tooSmallMessage} for the customized message used when the number is too small.
     * @param float|int|null $max Upper limit of the number. Defaults to `null`, meaning no upper limit.
     * See {@see $tooBigMessage} for the customized message used when the number is too big.
     * @param string $incorrectInputMessage Error message used when the value is not numeric.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $tooSmallMessage Error message used when the value is smaller than {@link $min}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{min}`: minimum value.
     * - `{value}`: actual value.
     * @param string $tooBigMessage Error message used when the value is bigger than {@link $max}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{max}`: maximum value.
     * - `{value}`: actual value.
     * @param string $integerPattern The regular expression for matching integers.
     * @param string $numberPattern The regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private bool $integerOnly = false,
        private float|int|null $min = null,
        private float|int|null $max = null,
        private string $incorrectInputMessage = 'The allowed types are integer, float and string.',
        private string $tooSmallMessage = 'Value must be no less than {min}.',
        private string $tooBigMessage = 'Value must be no greater than {max}.',
        private string $integerPattern = '/^\s*[+-]?\d+\s*$/',
        private string $numberPattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'number';
    }

    /**
     * Whether the value can only be an integer.
     *
     * @return bool Whether the value can only be an integer. Defaults to `false`.
     *
     * @see $integerOnly
     */
    public function isIntegerOnly(): bool
    {
        return $this->integerOnly;
    }

    /**
     * Get lower limit of the number. `null` means no lower limit.
     *
     * @return float|int|null Lower limit of the number.
     *
     * @see $min
     */
    public function getMin(): float|int|null
    {
        return $this->min;
    }

    /**
     * Get upper limit of the number. `null` means no upper limit.
     *
     * @return float|int|null Upper limit of the number.
     *
     * @see $max
     */
    public function getMax(): float|int|null
    {
        return $this->max;
    }

    /**
     * Get error message used when the value is not numeric.
     *
     * @return string Error message.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Get error message used when the value is smaller than {@link $min}.
     *
     * @return string Error message.
     *
     * @see $tooSmallMessage
     */
    public function getTooSmallMessage(): string
    {
        return $this->tooSmallMessage;
    }

    /**
     * Get error message used when the value is bigger than {@link $max}.
     *
     * @return string Error message.
     *
     * @see $tooBigMessage
     */
    public function getTooBigMessage(): string
    {
        return $this->tooBigMessage;
    }

    /**
     * Get the regular expression for matching integers.
     *
     * @return string The regular expression.
     *
     * @see $integerPattern
     */
    public function getIntegerPattern(): string
    {
        return $this->integerPattern;
    }

    /**
     * The regular expression for matching numbers.
     *
     * @return string The regular expression.
     *
     * @see $numberPattern
     */
    public function getNumberPattern(): string
    {
        return $this->numberPattern;
    }

    /**
     * Get error message used when value type does not match.
     *
     * @return string Error message.
     */
    public function getNotNumberMessage(): string
    {
        return $this->integerOnly ? 'Value must be an integer.' : 'Value must be a number.';
    }

    public function getOptions(): array
    {
        return [
            'asInteger' => $this->integerOnly,
            'min' => $this->min,
            'max' => $this->max,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'notNumberMessage' => [
                'template' => $this->getNotNumberMessage(),
                'parameters' => [],
            ],
            'tooSmallMessage' => [
                'template' => $this->tooSmallMessage,
                'parameters' => ['min' => $this->min],
            ],
            'tooBigMessage' => [
                'template' => $this->tooBigMessage,
                'parameters' => ['max' => $this->max],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'integerPattern' => $this->integerPattern,
            'numberPattern' => $this->numberPattern,
        ];
    }

    public function getHandler(): string
    {
        return NumberHandler::class;
    }
}
