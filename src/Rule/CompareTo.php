<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * CompareValidator compares the specified attribute value with another value.
 *
 * The value being compared with can be another attribute value
 * (specified via [[compareAttribute]]) or a constant (specified via
 * [[compareValue]]. When both are specified, the latter takes
 * precedence. If neither is specified, the attribute will be compared
 * with another attribute whose name is by appending "_repeat" to the source
 * attribute name.
 *
 * CompareValidator supports different comparison operators, specified
 * via the [[operator]] property.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to set the [[$type]]
 * to [[TYPE_NUMBER]] to enable numeric comparison.
 */
class CompareTo extends Rule
{
    /**
     * Constant for specifying the comparison [[type]] by numeric values.
     * @see type
     */
    private const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison [[type]] by numeric values.
     * @see type
     */
    private const TYPE_NUMBER = 'number';

    /**
     * @var mixed the constant value to be compared with. When both this property
     * and [[compareAttribute]] are set, this property takes precedence.
     * @see compareAttribute
     */
    private $compareValue;
    /**
     * @var string the type of the values being compared. The follow types are supported:
     *
     * - [[TYPE_STRING|string]]: the values are being compared as strings. No conversion will be done before comparison.
     * - [[TYPE_NUMBER|number]]: the values are being compared as numbers. String values will be converted into numbers before comparison.
     */
    private $type = self::TYPE_STRING;
    /**
     * @var string the operator for comparison. The following operators are supported:
     *
     * - `==`: check if two values are equal. The comparison is done is non-strict mode.
     * - `===`: check if two values are equal. The comparison is done is strict mode.
     * - `!=`: check if two values are NOT equal. The comparison is done is non-strict mode.
     * - `!==`: check if two values are NOT equal. The comparison is done is strict mode.
     * - `>`: check if value being validated is greater than the value being compared with.
     * - `>=`: check if value being validated is greater than or equal to the value being compared with.
     * - `<`: check if value being validated is less than the value being compared with.
     * - `<=`: check if value being validated is less than or equal to the value being compared with.
     *
     * When you want to compare numbers, make sure to also set [[type]] to `number`.
     */
    private $operator = '==';
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{compareValue}`: the value or the attribute label to be compared with
     * - `{compareAttribute}`: the label of the attribute to be compared with
     * - `{compareValueOrAttribute}`: the value or the attribute label to be compared with
     */
    private $message;

    private $validOperators = [
        '==' => 1,
        '===' => 1,
        '!=' => 1,
        '!==' => 1,
        '>' => 1,
        '>=' => 1,
        '<' => 1,
        '<=' => 1,
    ];

    public function getMessage(array $arguments): string
    {
        if ($this->message === null) {
            switch ($this->operator) {
                case '==':
                case '===':
                    return $this->formatMessage('Value must be equal to "{value}".', $arguments);
                case '!=':
                case '!==':
                    return $this->formatMessage('Value must not be equal to "{value}".', $arguments);
                case '>':
                    return $this->formatMessage('Value must be greater than "{value}".', $arguments);
                case '>=':
                    return $this->formatMessage('Value must be greater than or equal to "{value}".', $arguments);
                case '<':
                    return $this->formatMessage('Value must be less than "{value}".', $arguments);
                case '<=':
                    return $this->formatMessage('Value must be less than or equal to "{value}".', $arguments);
                default:
                    throw new \RuntimeException("Unknown operator: {$this->operator}");
            }
        }
    }

    public function __construct($value)
    {
        $this->compareValue = $value;
    }

    public function operator(string $operator): self
    {
        if (!isset($this->validOperators[$operator])) {
            throw new \InvalidArgumentException("Operator \"$operator\" is not supported.");
        }

        $this->operator = $operator;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function asString(): self
    {
        $this->type = self::TYPE_STRING;
    }

    public function asNumber(): self
    {
        $this->type = self::TYPE_NUMBER;
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        if ($this->compareValue === null) {
            throw new \RuntimeException('CompareValidator::compareValue must be set.');
        }
        if (!$this->compareValues($this->operator, $this->type, $value, $this->compareValue)) {
            $result->addError($this->getMessage([
                'value' => $this->compareValue,
            ]));
        }

        return $result;
    }

    /**
     * Compares two values with the specified operator.
     * @param string $operator the comparison operator
     * @param string $type the type of the values being compared
     * @param mixed $value the value being compared
     * @param mixed $compareValue another value being compared
     * @return bool whether the comparison using the specified operator is true.
     */
    protected function compareValues(string $operator, string $type, $value, $compareValue): bool
    {
        if ($type === self::TYPE_NUMBER) {
            $value = (float)$value;
            $compareValue = (float)$compareValue;
        } else {
            $value = (string)$value;
            $compareValue = (string)$compareValue;
        }
        switch ($operator) {
            case '==':
                return $value == $compareValue;
            case '===':
                return $value === $compareValue;
            case '!=':
                return $value != $compareValue;
            case '!==':
                return $value !== $compareValue;
            case '>':
                return $value > $compareValue;
            case '>=':
                return $value >= $compareValue;
            case '<':
                return $value < $compareValue;
            case '<=':
                return $value <= $compareValue;
            default:
                return false;
        }
    }
}
