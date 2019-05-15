<?php


namespace Yiisoft\Validator;

abstract class Rule
{
    private $when;

    abstract public function validateValue($value): Result;

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        $when = $this->when;
        $shouldValidate = $when($data, $attribute);
        if ($shouldValidate === false) {
            return new Result();
        }

        $value = $data->getValue($attribute);
        return $this->validateValue($value);
    }

    protected function formatMessage(string $message, array $arguments = []): string
    {
        $i18n = Yii::get('i18n', null, false);
        if (isset($i18n)) {
            return $i18n->format($message, $arguments);
        }

        return I18N::substitute($message, $arguments);
    }

    public function when(callable $callable): self
    {
        $this->when = $callable;
        return $this;
    }
}
