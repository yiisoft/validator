<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * CompareValidator compares the specified attribute value with another value.
 *
 * The value being compared with a constant {@see CompareTo::$compareValue}, which is set
 * in the constructor.
 *
 * CompareValidator supports different comparison operators, specified
 * via the {@see CompareTo::$operator}.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to call the {@see CompareTo::$asNumber}
 * to enable numeric comparison.
 */
final class CompareTo extends Rule
{
    /**
     * Constant for specifying the comparison as string values.
     * No conversion will be done before comparison.
     *
     * @see $type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison as numeric values.
     * String values will be converted into numbers before comparison.
     *
     * @see $type
     */
    public const TYPE_NUMBER = 'number';
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

    public function __construct(
        /**
         * @var mixed the constant value to be compared with.
         */
        private $compareValue,
        /**
         * @var string|null user-defined error message
         */
        private ?string $message = null,
        /**
         * @var string the type of the values being compared.
         */
        private string $type = self::TYPE_STRING,
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
         * When you want to compare numbers, make sure to also call {@see $asNumber}.
         */
        private string $operator = '==',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);

        if (!isset($this->validOperators[$operator])) {
            throw new InvalidArgumentException("Operator \"$operator\" is not supported.");
        }
    }

    private function getMessage(): string
    {
        if ($this->message !== null) {
            return $this->message;
        }

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
                throw new RuntimeException("Unknown operator: {$this->operator}");
        }
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!$this->compareValues($this->operator, $this->type, $value, $this->compareValue)) {
            $message = $this->formatMessage($this->getMessage(), ['value' => $this->compareValue]);
            $result->addError($message);
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
            $value = (float) $value;
            $compareValue = (float)$compareValue;
        } else {
            $value = (string) $value;
            $compareValue = (string) $compareValue;
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
        return array_merge(parent::getOptions(), [
            'compareValue' => $this->compareValue,
            'message' => $this->formatMessage($this->getMessage(), ['value' => $this->compareValue]),
            'type' => $this->type,
            'operator' => $this->operator,
        ]);
    }
}
