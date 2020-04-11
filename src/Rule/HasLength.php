<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

/**
 * StringValidator validates that the attribute value is of certain length.
 *
 * Note, this validator should only be used with string-typed attributes.
 */
class HasLength extends Rule
{
    use HasValidationMessage;

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
     * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
     */
    private string $tooShortMessage = 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.';
    /**
     * @var string user-defined error message used when the length of the value is greater than {@see $max}.
     */
    private string $tooLongMessage = 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.';
    /**
     * @var string user-defined error message used when the value is not a string.
     */
    protected string $message = 'This value must be a string.';
    /**
     * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     */
    protected string $encoding = 'UTF-8';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($this->message);
            return $result;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $result->addError($this->translateMessage($this->tooShortMessage, ['min' => $this->min]));
        }
        if ($this->max !== null && $length > $this->max) {
            $result->addError($this->translateMessage($this->tooLongMessage, ['min' => $this->max]));
        }

        return $result;
    }

    public function min(int $value): self
    {
        $new = clone $this;
        $new->min = $value;
        return $new;
    }

    public function max(int $value): self
    {
        $new = clone $this;
        $new->max = $value;
        return $new;
    }

    public function encoding(string $encoding): self
    {
        $new = clone $this;
        $new->encoding = $encoding;
        return $new;
    }

    public function tooShortMessage(string $message): self
    {
        $new = clone $this;
        $new->tooShortMessage = $message;
        return $new;
    }

    public function tooLongMessage(string $message): self
    {
        $new = clone $this;
        $new->tooLongMessage = $message;
        return $new;
    }
}
