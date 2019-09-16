<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * StringValidator validates that the attribute value is of certain length.
 *
 * Note, this validator should only be used with string-typed attributes.
 */
class HasLength extends Rule
{
    /**
     * @var int maximum length. If not set, it means no maximum length limit.
     * @see tooLongMessage for the customized message for a too long string.
     */
    private $max;
    /**
     * @var int minimum length. If not set, it means no minimum length limit.
     * @see tooShortMessage for the customized message for a too short string.
     */
    private $min;
    /**
     * @var string user-defined error message used when the value is not a string.
     */
    private $message = '{attribute} must be a string.';
    /**
     * @var string user-defined error message used when the length of the value is smaller than [[min]].
     */
    private $tooShortMessage = '{attribute} should contain at least {min, number} {min, plural, one{character} other{characters}}.';
    /**
     * @var string user-defined error message used when the length of the value is greater than [[max]].
     */
    private $tooLongMessage = '{attribute} should contain at most {max, number} {max, plural, one{character} other{characters}}.';

    /**
     * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     */
    protected $encoding = 'UTF-8';

    public function min(int $value): self
    {
        $this->min = $value;
        return $this;
    }

    public function max(int $value): self
    {
        $this->max = $value;
        return $this;
    }

    public function encoding(string $encoding): self
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $result->addError($this->formatMessage($this->tooShortMessage, ['min' => $this->min]));
        }
        if ($this->max !== null && $length > $this->max) {
            $result->addError($this->formatMessage($this->tooLongMessage, ['min' => $this->max]));
        }

        return $result;
    }
}
