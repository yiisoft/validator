<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Number extends Rule
{
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
        private ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        /**
         * @var callable|null
         */
        $when = null,
    ) {
        parent::__construct(skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result($this->formatter);

        if (is_bool($value) || !is_scalar($value)) {
            $result->addError($this->getNotANumberMessage(), parameters: ['value' => $value]);
            return $result;
        }

        $pattern = $this->asInteger ? $this->integerPattern : $this->numberPattern;

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $result->addError($this->getNotANumberMessage(), parameters: ['value' => $value]);
        } elseif ($this->min !== null && $value < $this->min) {
            $result->addError($this->tooSmallMessage, parameters: ['min' => $this->min]);
        } elseif ($this->max !== null && $value > $this->max) {
            $result->addError($this->tooBigMessage, parameters: ['max' => $this->max]);
        }

        return $result;
    }

    private function getNotANumberMessage(): string
    {
        return $this->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'asInteger' => $this->asInteger,
            'min' => $this->min,
            'max' => $this->max,
            'notANumberMessage' => $this->getNotANumberMessage(),
            'tooSmallMessage' => [$this->tooSmallMessage, ['min' => $this->min]],
            'tooBigMessage' => [$this->tooBigMessage, ['max' => $this->max]],
        ]);
    }
}
