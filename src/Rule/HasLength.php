<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
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
     * @var int|null maximum length. null means no maximum length limit.
     * @see tooLongMessage for the customized message for a too long string.
     */
    private ?int $max = null;
    /**
     * @var int|null minimum length. null means no minimum length limit.
     * @see tooShortMessage for the customized message for a too short string.
     */
    private ?int $min = null;
    /**
     * @var string user-defined error message used when the value is not a string.
     */
    private string $message = '{attribute} must be a string.';
    /**
     * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
     */
    private string $tooShortMessage = '{attribute} should contain at least {min, number} {min, plural, one{character} other{characters}}.';
    /**
     * @var string user-defined error message used when the length of the value is greater than {@see $max}.
     */
    private string $tooLongMessage = '{attribute} should contain at most {max, number} {max, plural, one{character} other{characters}}.';

    /**
     * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     */
    protected string $encoding = 'UTF-8';

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

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
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

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function tooShortMessage(string $message): self
    {
        $this->tooShortMessage = $message;

        return $this;
    }

    public function tooLongMessage(string $message): self
    {
        $this->tooLongMessage = $message;

        return $this;
    }
}
