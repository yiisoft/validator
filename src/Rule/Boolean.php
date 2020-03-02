<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

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
    private bool $strict = false;

    private string $message = 'The value must be either "{true}" or "{false}".';

    public function message(string $message): self
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }

    public function trueValue($value): self
    {
        $new = clone $this;
        $new->trueValue = $value;
        return $new;
    }

    public function falseValue($value): self
    {
        $new = clone $this;
        $new->falseValue = $value;
        return $new;
    }

    public function strict(bool $value): self
    {
        $new = clone $this;
        $new->strict = $value;
        return $new;
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
            $result->addError(
                $this->translateMessage(
                    $this->message,
                    [
                        'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                        'false' => $this->falseValue === false ? 'false' : $this->falseValue,
                    ]
                )
            );
        }

        return $result;
    }
}
