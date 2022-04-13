<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Number;

use Attribute;
use Closure;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Number
{
    public function __construct(
        /**
         * @var bool whether the value can only be an integer. Defaults to false.
         */
        public bool      $asInteger = false,
        /**
         * @var float|int lower limit of the number. Defaults to null, meaning no lower limit.
         *
         * @see tooSmallMessage for the customized message used when the number is too small.
         */
        public           $min = null,
        /**
         * @var float|int upper limit of the number. Defaults to null, meaning no upper limit.
         *
         * @see tooBigMessage for the customized message used when the number is too big.
         */
        public           $max = null,
        /**
         * @var string user-defined error message used when the value is smaller than {@link $min}.
         */
        public string    $tooSmallMessage = 'Value must be no less than {min}.',
        /**
         * @var string user-defined error message used when the value is bigger than {@link $max}.
         */
        public string    $tooBigMessage = 'Value must be no greater than {max}.',
        /**
         * @var string the regular expression for matching integers.
         */
        public string   $integerPattern = '/^\s*[+-]?\d+\s*$/',
        /**
         * @var string the regular expression for matching numbers. It defaults to a pattern
         * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
         */
        public string   $numberPattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    )
    {

    }

    private function getNotANumberMessage(): string
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
        ];
    }
}
