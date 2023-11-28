<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see AbstractNumber::$pattern}. Optionally,
 * you may configure the {@see AbstractNumber::$min} and {@see AbstractNumber::$max} to ensure the number is within a
 * certain range.
 *
 * @see NumberHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
abstract class AbstractNumber implements
    DumpedRuleInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * A default for {@see $incorrectInputMessage}.
     */
    protected const DEFAULT_INCORRECT_INPUT_MESSAGE = 'The allowed types are integer, float and string.';
    /**
     * A default for {@see $lessThanMinMessage}.
     */
    protected const DEFAULT_LESS_THAN_MIN_MESSAGE = 'Value must be no less than {min}.';
    /**
     * A default for {@see $greaterThanMaxMessage}.
     */
    protected const DEFAULT_GREATER_THAN_MAX_MESSAGE = 'Value must be no greater than {max}.';

    /**
     * @var string The regular expression for matching numbers.
     * @psalm-var non-empty-string
     */
    private string $pattern;

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
     * @param string $pattern The regular expression for matching numbers.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty. See
     * {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param WhenType $when
     */
    public function __construct(
        private float|int|null $min,
        private float|int|null $max,
        private string $incorrectInputMessage,
        private string $notNumberMessage,
        private string $lessThanMinMessage,
        private string $greaterThanMaxMessage,
        string $pattern,
        private mixed $skipOnEmpty,
        private bool $skipOnError,
        private Closure|null $when,
    ) {
        if ($pattern === '') {
            throw new InvalidArgumentException('Pattern can\'t be empty.');
        }

        $this->pattern = $pattern;
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
     * Get error message used when the value does not match {@see $pattern}
     *
     * @return string Error message.
     *
     * @see $notNumberMessage
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
     * @see $lessThanMinMessage
     */
    public function getLessThanMinMessage(): string
    {
        return $this->lessThanMinMessage;
    }

    /**
     * Get error message used when the value is bigger than {@link $max}.
     *
     * @return string Error message.
     *
     * @see $greaterThanMaxMessage
     */
    public function getGreaterThanMaxMessage(): string
    {
        return $this->greaterThanMaxMessage;
    }

    /**
     * The regular expression for matching numbers.
     *
     * @return string The regular expression.
     * @psalm-return non-empty-string
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
            'lessThanMinMessage' => [
                'template' => $this->lessThanMinMessage,
                'parameters' => ['min' => $this->min],
            ],
            'greaterThanMaxMessage' => [
                'template' => $this->greaterThanMaxMessage,
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
