<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

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
 * The format of the number must match the regular expression specified in {@see AbstractNumber::$pattern}. Optionally,
 * you may configure the {@see AbstractNumber::$min} and {@see AbstractNumber::$max}.
 * to ensure the number is within certain range.
 *
 * @see NumberHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
abstract class AbstractNumber implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
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
     * @param string $pattern The regular expression for matching numbers. It defaults to a pattern
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
        private float|int|null $min = null,
        private float|int|null $max = null,
        private string $incorrectInputMessage = 'The allowed types are integer, float and string.',
        private string $notNumberMessage = 'Value must be a number.',
        private string $tooSmallMessage = 'Value must be no less than {min}.',
        private string $tooBigMessage = 'Value must be no greater than {max}.',
        private string $pattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
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
     * Get error message used when value type does not match.
     *
     * @return string Error message.
     */
    public function getNotNumberMessage(): string
    {
        return $this->notNumberMessage;
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
     * The regular expression for matching numbers.
     *
     * @return string The regular expression.
     *
     * @see $pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getOptions(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'notNumberMessage' => [
                'template' => $this->notNumberMessage,
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
            'pattern' => $this->pattern,
        ];
    }

    public function getHandler(): string
    {
        return NumberHandler::class;
    }
}
