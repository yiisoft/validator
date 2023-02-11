<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Stringable;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function gettype;
use function in_array;

/**
 * Compares the specified value with "target" value provided directly or within an attribute.
 *
 * @see AbstractCompare
 * @see Equal
 * @see GreaterThan
 * @see GreaterThanOrEqual
 * @see LessThan
 * @see LessThanOrEqual
 * @see Compare
 * @see NotEqual
 */
final class CompareHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof AbstractCompare) {
            throw new UnexpectedRuleException(AbstractCompare::class, $rule);
        }

        $result = new Result();
        if (!$this->isInputCorrect($rule->getType(), $value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        /** @var mixed $targetValue */
        $targetValue = $rule->getTargetValue();
        $targetAttribute = $rule->getTargetAttribute();

        if ($targetValue === null && $targetAttribute !== null) {
            /** @var mixed $targetValue */
            $targetValue = $context->getDataSet()->getAttributeValue($targetAttribute);
            if (!$this->isInputCorrect($rule->getType(), $targetValue)) {
                return $result->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'type' => get_debug_type($targetValue),
                ]);
            }
        }

        if ($this->compareValues($rule->getType(), $rule->getOperator(), $value, $targetValue)) {
            return new Result();
        }

        return (new Result())->addError($rule->getMessage(), [
            'attribute' => $context->getTranslatedAttribute(),
            'targetValue' => $this->getFormattedValue($rule->getType(), $rule->getTargetValue()),
            'targetAttribute' => $targetAttribute,
            'targetAttributeValue' => $targetAttribute !== null ? $this->getFormattedValue($rule->getType(), $targetValue) : null,
            'targetValueOrAttribute' => $targetAttribute ?? $this->getFormattedValue($rule->getType(), $targetValue),
            'value' => $this->getFormattedValue($rule->getType(), $value),
        ]);
    }

    /**
     * Checks whether the validated value has correct type depending on selected {@see AbstractCompare::$type}.
     *
     * @param string $type The type of the values being compared ({@see AbstractCompare::$type}).
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param mixed $value The validated value.
     *
     * @return bool `true` if value is correct and `false` otherwise.
     */
    private function isInputCorrect(string $type, mixed $value): bool
    {
        return $type !== CompareType::ORIGINAL ? $this->isValueAllowedForTypeCasting($value) : true;
    }

    /**
     * Checks whether the validated value is allowed for types that require type casting - {@see CompareType::NUMBER}
     * and {@see CompareType::STRING}.
     *
     * @param mixed $value The Validated value.
     *
     * @return bool `true` if value is allowed and `false` otherwise.
     */
    private function isValueAllowedForTypeCasting(mixed $value): bool
    {
        return $value === null || is_scalar($value) || $value instanceof Stringable;
    }

    /**
     * Gets representation of the value for using with error parameter.
     *
     * @param string $type The type of the values being compared ({@see AbstractCompare::$type}).
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param mixed $value The мalidated value.
     *
     * @return scalar|null Formatted value.
     */
    private function getFormattedValue(string $type, mixed $value): int|float|string|bool|null
    {
        if ($value === null || is_scalar($value)) {
            return $value;
        }

        if ($value instanceof Stringable && $type !== CompareType::ORIGINAL) {
            return (string) $value;
        }

        return get_debug_type($value);
    }

    /**
     * Compares two values according to the specified type and operator.
     *
     * @param string $operator The comparison operator. One of `==`, `===`, `!=`, `!==`, `>`, `>=`, `<`, `<=`.
     * @param string $type The type of the values being compared ({@see AbstractCompare::$type}).
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param mixed $value The validated value.
     * @param mixed $targetValue "Target" value set in rule options.
     *
     * @return bool Whether the result of comparison using the specified operator is true.
     */
    private function compareValues(string $type, string $operator, mixed $value, mixed $targetValue): bool
    {
        if (!in_array($operator, ['==', '===', '!=', '!=='])) {
            if ($type === CompareType::STRING) {
                $value = (string) $value;
                $targetValue = (string) $targetValue;
            } elseif ($type === CompareType::NUMBER) {
                $value = $this->normalizeNumber($value);
                $targetValue = $this->normalizeNumber($targetValue);
            }
        }

        return match ($operator) {
            '==' => $this->checkValuesAreEqual($type, $value, $targetValue),
            '===' => $this->checkValuesAreEqual($type, $value, $targetValue, strict: true),
            '!=' => !$this->checkValuesAreEqual($type, $value, $targetValue),
            '!==' => !$this->checkValuesAreEqual($type, $value, $targetValue, strict: true),
            '>' => $value > $targetValue,
            '>=' => $value >= $targetValue,
            '<' => $value < $targetValue,
            '<=' => $value <= $targetValue,
        };
    }

    /**
     * Checks whether a validated value equals to "target" value. For types other than {@see CompareType::ORIGINAL},
     * handles strict comparison before type casting and takes edge cases for float numbers into account.
     *
     * @param string $type The type of the values being compared ({@see AbstractCompare::$type}).
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param mixed $value The validated value.
     * @param mixed $targetValue "Target" value set in rule options.
     * @param bool $strict Whether the values must be equal (when set to `false`, default) / strictly equal (when set to
     * `true`).
     *
     * @return bool `true` if values are equal and `false` otherwise.
     */
    private function checkValuesAreEqual(string $type, mixed $value, mixed $targetValue, bool $strict = false): bool
    {
        if ($type === CompareType::ORIGINAL) {
            return $strict ? $value === $targetValue : $value == $targetValue;
        }

        if ($strict && gettype($value) !== gettype($targetValue)) {
            return false;
        }

        return match ($type) {
            CompareType::STRING => (string) $value === (string) $targetValue,
            CompareType::NUMBER => $this->checkFloatsAreEqual(
                $this->normalizeNumber($value),
                $this->normalizeNumber($targetValue),
            ),
        };
    }

    /**
     * Checks whether a validated float number equals to "target" float number. Handles a known problem of losing
     * precision during arithmetical operations.
     *
     * @param float $value The validated number.
     * @param float $targetValue "Target" number set in rule options.
     *
     * @return bool `true` if numbers are equal and `false` otherwise.
     *
     * @link https://floating-point-gui.de/
     */
    private function checkFloatsAreEqual(float $value, float $targetValue): bool
    {
        return abs($value - $targetValue) < PHP_FLOAT_EPSILON;
    }

    /**
     * Normalizes number that might be stored in a different type to float number.
     *
     * @param mixed $number Raw number. Can be within an object implementing {@see Stringable} interface or other
     * primitive type, such as `int`, `float`, `string`.
     *
     * @return float Float number ready for comparison.
     */
    private function normalizeNumber(mixed $number): float
    {
        if ($number instanceof Stringable) {
            $number = (string) $number;
        }

        return (float) $number;
    }
}
