<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Number implements ParametrizedRuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var bool whether the value can only be an integer. Defaults to false.
         */
        private bool $asInteger = false,
        /**
         * @var float|int lower limit of the number. Defaults to null, meaning no lower limit.
         *
         * @see tooSmallMessage for the customized message used when the number is too small.
         */
        private $min = null,
        /**
         * @var float|int upper limit of the number. Defaults to null, meaning no upper limit.
         *
         * @see tooBigMessage for the customized message used when the number is too big.
         */
        private $max = null,
        /**
         * @var string user-defined error message used when the value is smaller than {@link $min}.
         */
        private string $tooSmallMessage = 'Value must be no less than {min}.',
        /**
         * @var string user-defined error message used when the value is bigger than {@link $max}.
         */
        private string $tooBigMessage = 'Value must be no greater than {max}.',
        /**
         * @var string the regular expression for matching integers.
         */
        private string $integerPattern = '/^\s*[+-]?\d+\s*$/',
        /**
         * @var string the regular expression for matching numbers. It defaults to a pattern
         * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
         */
        private string $numberPattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
    }

    /**
     * @return bool
     */
    public function isAsInteger(): bool
    {
        return $this->asInteger;
    }


    public function getMin()
    {
        return $this->min;
    }


    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return string
     */
    public function getTooSmallMessage(): string
    {
        return $this->tooSmallMessage;
    }

    /**
     * @return string
     */
    public function getTooBigMessage(): string
    {
        return $this->tooBigMessage;
    }

    /**
     * @return string
     */
    public function getIntegerPattern(): string
    {
        return $this->integerPattern;
    }

    /**
     * @return string
     */
    public function getNumberPattern(): string
    {
        return $this->numberPattern;
    }

    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }

    public function getNotANumberMessage(): string
    {
        return $this->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
    }

    public function getOptions(): array
    {
        return [
            'asInteger' => $this->asInteger,
            'min' => $this->min,
            'max' => $this->max,
            'notANumberMessage' => [
                'message' => $this->getNotANumberMessage(),
            ],
            'tooSmallMessage' => [
                'message' => $this->tooSmallMessage,
                'parameters' => ['min' => $this->min],
            ],
            'tooBigMessage' => [
                'message' => $this->tooBigMessage,
                'parameters' => ['max' => $this->max],
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
            'integerPattern' => $this->integerPattern,
            'numberPattern' => $this->numberPattern,
        ];
    }
}
