<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * NumberValidator validates that the attribute value is a number.
 *
 * The format of the number must match the regular expression specified in [[integerPattern]] or [[numberPattern]].
 * Optionally, you may configure the [[max]] and [[min]] properties to ensure the number
 * is within certain range.
 */
class Number extends Rule
{
    /**
     * @var bool whether the attribute value can only be an integer. Defaults to false.
     */
    private $asInteger = false;
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
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    private $tooBigMessage;
    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    private $tooSmallMessage;
    /**
     * @var string the regular expression for matching integers.
     */
    private $integerPattern = '/^\s*[+-]?\d+\s*$/';
    /**
     * @var string the regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     */
    private $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    private $message;

    private function getNotANumberMessage(array $arguments)
    {
        if ($this->message !== null) {
            return $this->formatMessage($this->message, $arguments);
        }

        if ($this->asInteger === true) {
            return $this->formatMessage('Value must be an integer.', $arguments);
        }

        return $this->formatMessage('Value must be a number.', $arguments);
    }

    private function getTooBigMessage(array $arguments): string
    {
        if ($this->tooBigMessage === null) {
            return $this->formatMessage('Value must be no greater than {max}.', $arguments);
        }

        return $this->formatMessage($this->tooBigMessage, $arguments);
    }

    private function getTooSmallMessage(array $arguments): string
    {
        if ($this->tooSmallMessage === null) {
            return $this->formatMessage('Value must be no less than {min}.', $arguments);
        }

        return $this->formatMessage($this->tooSmallMessage, $arguments);
    }

    public function integer(): self
    {
        $this->asInteger = true;
        return $this;
    }

    public function min($value): self
    {
        $this->min = $value;
        return $this;
    }

    public function max($value): self
    {
        $this->max = $value;
        return $this;
    }

    public function tooSmallMessage(string $message): self
    {
        $this->tooSmallMessage = $message;
        return $this;
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        if ($this->isNotNumber($value)) {
            $result->addError($this->getNotANumberMessage(['value' => $value]));
            return $result;
        }

        $pattern = $this->asInteger ? $this->integerPattern : $this->numberPattern;

        if (!preg_match($pattern, static::normalizeNumber($value))) {
            $result->addError($this->getNotANumberMessage(['value' => $value]));
        } elseif ($this->min !== null && $value < $this->min) {
            $result->addError($this->getTooSmallMessage(['min' => $this->min]));
        } elseif ($this->max !== null && $value > $this->max) {
            $result->addError($this->getTooBigMessage(['max' => $this->max]));
        }

        return $result;
    }

    /*
     * @param mixed $value the data value to be checked.
     */
    private function isNotNumber($value)
    {
        return is_array($value)
        || (is_object($value) && !method_exists($value, '__toString'))
        || (!is_object($value) && !is_scalar($value) && $value !== null);
    }

    /**
     * Returns string representation of number value with replaced commas to dots, if decimal point
     * of current locale is comma.
     * @param int|float|string $value
     * @return string
     */
    public static function normalizeNumber($value): string
    {
        $value = (string)$value;
        $localeInfo = localeconv();
        $decimalSeparator = $localeInfo['decimal_point'] ?? null;
        if ($decimalSeparator !== null && $decimalSeparator !== '.') {
            $value = str_replace($decimalSeparator, '.', $value);
        }
        return $value;
    }
}
