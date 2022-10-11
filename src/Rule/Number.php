<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Number implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

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

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'number';
    }

    public function isAsInteger(): bool
    {
        return $this->asInteger;
    }

    public function getMin(): float|int|null
    {
        return $this->min;
    }

    public function getMax(): float|int|null
    {
        return $this->max;
    }

    public function getTooSmallMessage(): string
    {
        return $this->tooSmallMessage;
    }

    public function getTooBigMessage(): string
    {
        return $this->tooBigMessage;
    }

    public function getIntegerPattern(): string
    {
        return $this->integerPattern;
    }

    public function getNumberPattern(): string
    {
        return $this->numberPattern;
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
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'integerPattern' => $this->integerPattern,
            'numberPattern' => $this->numberPattern,
        ];
    }

    public function getHandlerClassName(): string
    {
        return NumberHandler::class;
    }
}
