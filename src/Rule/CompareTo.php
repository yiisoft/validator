<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * CompareValidator compares the specified attribute value with another value.
 *
 * The value being compared with a constant [[compareValue]], which is set
 * in the constructor.
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
     *
     * @see type
     */
    private const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison [[type]] by numeric values.
     *
     * @see type
     */
    private const TYPE_NUMBER = 'number';

    /**
     * @var mixed the constant value to be compared with.
     */
    private $compareValue;
    /**
     * @var string the type of the values being compared. The follow types are supported:
     *
     * - [[TYPE_STRING|string]]: the values are being compared as strings. No conversion will be done before comparison.
     * - [[TYPE_NUMBER|number]]: the values are being compared as numbers. String values will be converted into numbers before comparison.
     */
    private string $type = self::TYPE_STRING;
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
    private string $operator = '==';

    private array $validOperators = [
        '==' => 1,
        '===' => 1,
        '!=' => 1,
        '!==' => 1,
        '>' => 1,
        '>=' => 1,
        '<' => 1,
        '<=' => 1,
    ];

    private function getMessage(): string
    {
        switch ($this->operator) {
            case '==':
            case '===':
                return 'Value must be equal to "{value}".';
            case '!=':
            case '!==':
                return 'Value must not be equal to "{value}".';
            case '>':
                return 'Value must be greater than "{value}".';
            case '>=':
                return 'Value must be greater than or equal to "{value}".';
            case '<':
                return 'Value must be less than "{value}".';
            case '<=':
                return 'Value must be less than or equal to "{value}".';
            default:
                throw new \RuntimeException("Unknown operator: {$this->operator}");
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

        $new = clone $this;
        $new->operator = $operator;
        return $new;
    }

    public function asString(): self
    {
        $new = clone $this;
        $new->type = self::TYPE_STRING;
        return $new;
    }

    public function asNumber(): self
    {
        $new = clone $this;
        $new->type = self::TYPE_NUMBER;
        return $new;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if ($this->compareValue === null) {
            throw new \RuntimeException('CompareValidator::compareValue must be set.');
        }

        if (!$this->compareValues($this->operator, $this->type, $value, $this->compareValue)) {
            $result->addError(
                $this->translateMessage(
                    $this->getMessage(),
                    [
                        'value' => $this->compareValue,
                    ]
                )
            );
        }

        return $result;
    }

    /**
     * Compares two values with the specified operator.
     *
     * @param string $operator the comparison operator
     * @param string $type the type of the values being compared
     * @param mixed $value the value being compared
     * @param mixed $compareValue another value being compared
     *
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

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'type' => $this->type,
                'operator' => $this->operator,
                'compareValue' => $this->compareValue,
                'message' => $this->translateMessage($this->getMessage(), ['value' => $this->compareValue]),
            ],
        );
    }
}
