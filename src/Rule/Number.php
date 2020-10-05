<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\AbstractRule;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;

/**
 * NumberValidator validates that the attribute value is a number.
 *
 * The format of the number must match the regular expression specified in [[integerPattern]] or [[numberPattern]].
 * Optionally, you may configure the [[max]] and [[min]] properties to ensure the number
 * is within certain range.
 */
class Number extends AbstractRule
{
    /**
     * @var bool whether the attribute value can only be an integer. Defaults to false.
     */
    private bool $asInteger = false;
    /**
     * @var int|float upper limit of the number. Defaults to null, meaning no upper limit.
     * @see tooBigMessage for the customized message used when the number is too big.
     */
    private $max;
    /**
     * @var int|float lower limit of the number. Defaults to null, meaning no lower limit.
     * @see tooSmallMessage for the customized message used when the number is too small.
     */
    private $min;
    /**
     * @var string user-defined error message used when the value is bigger than {@link $max}.
     */
    private string $tooBigMessage = 'Value must be no greater than {max}.';
    /**
     * @var string user-defined error message used when the value is smaller than {@link $min}.
     */
    private string $tooSmallMessage = 'Value must be no less than {min}.';
    /**
     * @var string the regular expression for matching integers.
     */
    private string $integerPattern = '/^\s*[+-]?\d+\s*$/';
    /**
     * @var string the regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     */
    private string $numberPattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if (!is_scalar($value)) {
            $result->addError($this->translateMessage($this->getNotANumberMessage(), ['value' => $value]));
            return $result;
        }

        $pattern = $this->asInteger ? $this->integerPattern : $this->numberPattern;

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $result->addError($this->translateMessage($this->getNotANumberMessage(), ['value' => $value]));
        } elseif ($this->min !== null && $value < $this->min) {
            $result->addError($this->translateMessage($this->tooSmallMessage, ['min' => $this->min]));
        } elseif ($this->max !== null && $value > $this->max) {
            $result->addError($this->translateMessage($this->tooBigMessage, ['max' => $this->max]));
        }

        return $result;
    }

    public function integer(): self
    {
        $new = clone $this;
        $new->asInteger = true;
        return $new;
    }

    public function min($value): self
    {
        $new = clone $this;
        $new->min = $value;
        return $new;
    }

    public function max($value): self
    {
        $new = clone $this;
        $new->max = $value;
        return $new;
    }

    public function tooSmallMessage(string $message): self
    {
        $new = clone $this;
        $new->tooSmallMessage = $message;
        return $new;
    }

    public function tooBigMessage(string $message): self
    {
        $new = clone $this;
        $new->tooBigMessage = $message;
        return $new;
    }

    private function getNotANumberMessage(): string
    {
        if ($this->asInteger === true) {
            return 'Value must be an integer.';
        }
        return 'Value must be a number.';
    }

    public function getName(): string
    {
        return 'number';
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'notANumberMessage' => $this->translateMessage($this->getNotANumberMessage()),
                'asInteger' => $this->asInteger,
                'min' => $this->min,
                'tooSmallMessage' => $this->translateMessage($this->tooSmallMessage, ['min' => $this->min]),
                'max' => $this->max,
                'tooBigMessage' => $this->translateMessage($this->tooBigMessage, ['max' => $this->max]),
            ],
        );
    }
}
