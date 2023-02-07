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
 * Compares the specified value with another value.
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
        if (!$this->isInputCorrect($rule, $value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $targetValue = $rule->getTargetValue();
        $targetAttribute = $rule->getTargetAttribute();

        if ($targetValue === null && $targetAttribute !== null) {
            /** @var mixed $targetValue */
            $targetValue = $context->getDataSet()->getAttributeValue($targetAttribute);
            if (!$this->isInputCorrect($rule, $targetValue)) {
                return $result->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'type' => get_debug_type($targetValue),
                ]);
            }
        }

        if ($this->compareValues($rule->getOperator(), $rule->getType(), $value, $targetValue)) {
            return new Result();
        }

        return (new Result())->addError($rule->getMessage(), [
            'attribute' => $context->getTranslatedAttribute(),
            'targetValue' => $rule->getTargetValue(),
            'targetAttribute' => $targetAttribute,
            'targetValueOrAttribute' => isset($targetValue) ? $this->getFormattedValue($targetValue) : $targetAttribute,
            'value' => $this->getFormattedValue($value),
        ]);
    }

    private function isInputCorrect(AbstractCompare $rule, mixed $value)
    {
        return $rule->getType() !== CompareType::ORIGINAL ? $this->isValueSimple($value) : true;
    }

    private function isValueSimple(mixed $value): bool
    {
        return $value === null || is_scalar($value) || $value instanceof Stringable;
    }

    private function getFormattedValue(mixed $value): int|float|string|Stringable|bool|null
    {
        return $this->isValueSimple($value) ? $value : get_debug_type($value);
    }

    /**
     * Compares two values with the specified operator.
     *
     * @param string $operator The comparison operator. One of `==`, `===`, `!=`, `!==`, `>`, `>=`, `<`, `<=`.
     * @param string $type The type of the values being compared.
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param mixed $value The value being compared.
     * @param mixed $targetValue Another value being compared.
     *
     * @return bool Whether the result of comparison using the specified operator is true.
     */
    private function compareValues(string $operator, string $type, mixed $value, mixed $targetValue): bool
    {
        if (!in_array($operator, ['==', '===', '!=', '!=='])) {
            if ($type === CompareType::STRING) {
                $value = (string) $value;
                $targetValue = (string) $targetValue;
            } elseif ($type === CompareType::NUMBER) {
                $value = $this->prepareNumber($value);
                $targetValue = $this->prepareNumber($targetValue);
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
                $this->prepareNumber($value),
                $this->prepareNumber($targetValue),
            ),
        };
    }

    private function checkFloatsAreEqual(float $value, float $targetValue): bool
    {
        return abs($value - $targetValue) < PHP_FLOAT_EPSILON;
    }

    private function prepareNumber(int|float|string|Stringable|bool|null $number): float
    {
        if ($number instanceof Stringable) {
            $number = (string) $number;
        }

        return (float) $number;
    }
}
