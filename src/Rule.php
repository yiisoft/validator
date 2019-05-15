<?php


namespace Yiisoft\Validator;

abstract class Rule
{
    private $when;

    private $skipOnEmpty = false;

    abstract public function validateValue($value): Result;

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        // TODO: consider moving out of Rule
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
//        $i18n = Yii::get('i18n', null, false);
//        if (isset($i18n)) {
//            return $i18n->format($message, $arguments);
//        }
//
//        return I18N::substitute($message, $arguments);
        return $message;
    }

    /**
     * @param callable $callable a PHP callable whose return value determines whether this validator should be applied.
     * The signature of the callable should be `function ($model, $attribute)`, where `$model` and `$attribute`
     * refer to the model and the attribute currently being validated. The callable should return a boolean value.
     *
     * This property is mainly provided to support conditional validation on the server-side.
     * If this property is not set, this validator will be always applied on the server-side.
     *
     * The following example will enable the validator only when the country currently selected is USA:
     *
     * ```php
     * function (DataSet $data, string $attribute) {
     *     return $data->getValue($attribute) == Country::USA;
     * }
     * ```
     *
     * @return $this
     */
    public function when(callable $callable): self
    {
        $this->when = $callable;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function skipOnEmpty(bool $value): self
    {
        $this->skipOnEmpty = $value;
        return $this;
    }
}
