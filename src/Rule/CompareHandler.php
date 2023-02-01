<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

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
        if ($value !== null && !is_scalar($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $targetAttribute = $rule->getTargetAttribute();
        $targetValue = $rule->getTargetValue();

        if ($targetValue === null && $targetAttribute !== null) {
            /** @var mixed $targetValue */
            $targetValue = $context->getDataSet()->getAttributeValue($targetAttribute);
            if (!is_scalar($targetValue)) {
                return $result->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'type' => get_debug_type($targetValue),
                ]);
            }
        }

        if ($this->compareValues($rule->getOperator(), $rule->getType(), $value, $targetValue)) {
            return $result;
        }

        return $result->addError($rule->getMessage(), [
            'attribute' => $context->getTranslatedAttribute(),
            'targetValue' => $rule->getTargetValue(),
            'targetAttribute' => $rule->getTargetAttribute(),
            'targetValueOrAttribute' => $targetValue ?? $targetAttribute,
            'value' => $value,
        ]);
    }

    /**
     * Compares two values with the specified operator.
     *
     * @param string $operator The comparison operator. One of `==`, `===`, `!=`, `!==`, `>`, `>=`, `<`, `<=`.
     * @param string $type The type of the values being compared.
     * @psalm-param AbstractCompare::TYPE_* $type
     *
     * @param mixed $value The value being compared.
     * @param mixed $targetValue Another value being compared.
     *
     * @return bool Whether the result of comparison using the specified operator is true.
     */
    private function compareValues(string $operator, string $type, mixed $value, mixed $targetValue): bool
    {
        if (!in_array($operator, ['==', '===', '!=', '!=='])) {
            [$value, $targetValue] = $this->getTypeCastedValues($type, $value, $targetValue);
        }

        return match ($operator) {
            '==' => $this->assertEquals($type, $value, $targetValue),
            '===' => $this->assertEquals($type, $value, $targetValue, strict: true),
            '!=' => $this->assertNotEquals($type, $value, $targetValue),
            '!==' => $this->assertNotEquals($type, $value, $targetValue, strict: true),
            '>' => $value > $targetValue,
            '>=' => $value >= $targetValue,
            '<' => $value < $targetValue,
            '<=' => $value <= $targetValue,
        };
    }

    private function assertEquals(
        string $type,
        mixed $value,
        mixed $targetValue,
        bool $strict = false,
    ): bool {
        if ($strict && gettype($value) !== gettype($targetValue)) {
            return false;
        }

        [$value, $targetValue] = $this->getTypeCastedValues($type, $value, $targetValue);

        if ($type === AbstractCompare::TYPE_NUMBER) {
            return $this->assertFloatsEqual($value, $targetValue);
        }

        return $strict ? $value === $targetValue : $value == $targetValue;
    }

    private function assertNotEquals(
        string $type,
        mixed $value,
        mixed $targetValue,
        bool $strict = false,
    ): bool {
        if ($strict && gettype($value) !== gettype($targetValue)) {
            return true;
        }

        [$value, $targetValue] = $this->getTypeCastedValues($type, $value, $targetValue);

        if ($type === AbstractCompare::TYPE_NUMBER) {
            return !$this->assertFloatsEqual($value, $targetValue);
        }

        return $strict ? $value !== $targetValue : $value != $targetValue;
    }

    private function assertFloatsEqual(float $value, float $targetValue): bool
    {
        return abs($value - $targetValue) < PHP_FLOAT_EPSILON;
    }

    private function getTypeCastedValues(string $type, mixed $value, mixed $targetValue)
    {
        return $type === AbstractCompare::TYPE_NUMBER
            ? [(float) $value, (float) $targetValue]
            : [(string) $value, (string) $targetValue];
    }
}
