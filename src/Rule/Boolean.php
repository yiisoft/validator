<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * BooleanValidator checks if the attribute value is a boolean value or a value corresponding to it.
 */
class Boolean extends Rule
{
    /**
     * @var mixed the value representing true status. Defaults to '1'.
     */
    private $trueValue = '1';
    /**
     * @var mixed the value representing false status. Defaults to '0'.
     */
    private $falseValue = '0';
    /**
     * @var bool whether the comparison to [[trueValue]] and [[falseValue]] is strict.
     * When this is true, the attribute value and type must both match those of [[trueValue]] or [[falseValue]].
     * Defaults to false, meaning only the value needs to be matched.
     */
    private $strict = false;

    /**
     * @var string
     */
    private $message;

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function trueValue($value): self
    {
        $this->trueValue = $value;
        return $this;
    }

    public function falseValue($value): self
    {
        $this->falseValue = $value;
        return $this;
    }

    public function strict(bool $value): self
    {
        $this->strict = $value;
        return $this;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        if ($this->strict) {
            $valid = $value === $this->trueValue || $value === $this->falseValue;
        } else {
            $valid = $value == $this->trueValue || $value == $this->falseValue;
        }

        $result = new Result();

        if (!$valid) {
            $result->addError($this->getMessage());
        }

        return $result;
    }

    private function getMessage(): string
    {
        $arguments = [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
            'false' => $this->falseValue === false ? 'false' : $this->falseValue,
        ];

        if ($this->message === null) {
            return $this->formatMessage('The value must be either "{true}" or "{false}".', $arguments);
        }

        return $this->formatMessage($this->message, $arguments);
    }
}
