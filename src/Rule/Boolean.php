<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Validator\Rule;

use Yii\Validator\Rule;

/**
 * BooleanValidator checks if the attribute value is a boolean value.
 *
 * Possible boolean values can be configured via the [[trueValue]] and [[falseValue]] properties.
 * And the comparison can be either [[strict]] or not.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
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
     * @translate
     */
    private $message = '{attribute} must be either "{true}" or "{false}".';

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

    /**
     * {@inheritdoc}
     */
    public function validateValue($value): Result
    {
        if ($this->strict) {
            $valid = $value === $this->trueValue || $value === $this->falseValue;
        } else {
            $valid = $value == $this->trueValue || $value == $this->falseValue;
        }

        $result = new Result();

        if (!$valid) {
            $message = $this->formatMessage($this->message, [
                'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                'false' => $this->falseValue === false ? 'false' : $this->falseValue,
            ]);
            $result->addError($message);
        }

        return $result;
    }

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        $value = $data->getValue($attribute);
        return $this->validateValue($value);
    }
}
