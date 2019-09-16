<?php


namespace Yiisoft\Validator;

abstract class Rule
{
    private $skipOnEmpty = false;

    public function validate($value): Result
    {
        if ($this->skipOnEmpty && empty($value)) {
            return new Result();
        }

        return $this->validateValue($value);
    }

    abstract public function validateValue($value): Result;

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
     * @return $this
     */
    public function skipOnEmpty(bool $value): self
    {
        $this->skipOnEmpty = $value;
        return $this;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value the value to be checked
     * @return bool whether the value is empty
     */
    protected function isEmpty($value)
    {
        return $value === null || $value === [] || $value === '';
    }
}
