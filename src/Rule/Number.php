<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\DataSet;
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
    private $integer = false;
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

    public function __construct()
    {
        $this->message = $this->integer ? '{attribute} must be an integer.'
            : '{attribute} must be a number.';

        $this->tooSmallMessage = '{attribute} must be no less than {min}.';
        $this->tooBigMessage = '{attribute} must be no greater than {max}.';
    }

    public function integer(): self
    {
        $this->integer = true;
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

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        $result = new Result();

        $value = $data->getValue($attribute);
        if ($this->isNotNumber($value)) {
            $result->addError($this->message);
            return $result;
        }
        $pattern = $this->integer ? $this->integerPattern : $this->numberPattern;

        if (!preg_match($pattern, StringHelper::normalizeNumber($value))) {
            $result->addError($this->message);
        }
        if ($this->min !== null && $value < $this->min) {
            $result->addError($this->formatMessage($this->tooSmallMessage, ['min' => $this->min]));
        }
        if ($this->max !== null && $value > $this->max) {
            $result->addError($this->formatMessage($this->tooBigMessage, ['max' => $this->max]));
        }

        return $result;
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        if ($this->isNotNumber($value)) {
            $result->addError('Value is not a number.');
            return $result;
        }

        $pattern = $this->integer ? $this->integerPattern : $this->numberPattern;

        if (!preg_match($pattern, StringHelper::normalizeNumber($value))) {
            $result->addError($this->message);
        } elseif ($this->min !== null && $value < $this->min) {
            $result->addError($this->formatMessage($this->tooSmallMessage, ['min' => $this->min]));
        } elseif ($this->max !== null && $value > $this->max) {
            $result->addError($this->formatMessage($this->tooBigMessage, ['max' => $this->max]));
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
}
