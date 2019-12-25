<?php


namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule
{
    private bool $skipOnEmpty = false;

    /**
     * @param mixed $value
     * @param DataSetInterface|null $dataSet
     * @return Result
     */
    final public function validate($value, DataSetInterface $dataSet = null): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        return $this->validateValue($value, $dataSet);
    }

    abstract protected function validateValue($value, DataSetInterface $dataSet = null): Result;

    protected function formatMessage(string $message, array $arguments = []): string
    {
        $replacements = [];
        foreach ($arguments as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }

            $replacements['{' . $key . '}'] = $value;
        }

        // TODO: move it to upper level and make it configurable?
        return strtr($message, $replacements);
    }

    /**
     * @param bool $value
     * @return self
     */
    public function skipOnEmpty(bool $value): self
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value the value to be checked
     * @return bool whether the value is empty
     */
    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
