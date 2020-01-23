<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * RequiredValidator validates that the specified attribute does not have null or empty value.
 */
class Required extends Rule
{
    /**
     * @var bool whether the comparison between the attribute value and [[requiredValue]] is strict.
     * When this is true, both the values and types must match.
     * Defaults to false, meaning only the values need to match.
     * Note that when [[requiredValue]] is null, if this property is true, the validator will check
     * if the attribute value is null; If this property is false, the validator will call [[isEmpty]]
     * to check if the attribute value is empty.
     */
    private bool $strict = false;
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{requiredValue}`: the value of [[requiredValue]]
     */
    private string $message = 'Value cannot be blank.';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if (($this->strict && $value !== null) || (!$this->strict && !$this->isEmpty(is_string($value) ? trim($value) : $value))) {
            return $result;
        }

        $result->addError($this->formatMessage($this->message));
        return $result;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function strict(): self
    {
        $this->strict = true;
        return $this;
    }
}
