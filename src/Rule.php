<?php


namespace Yiisoft\Validator;

abstract class Rule
{
    abstract public function validateValue($value): Result;

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        $value = $data->getValue($attribute);
        return $this->validateValue($value);
    }

    protected function formatMessage(string $message, array $arguments = []): string
    {
        $i18n = Yii::get('i18n', null, false);
        if (isset($i18n)) {
            return $i18n->format($message, $params);
        }

        return I18N::substitute($message, $params);
    }
}
