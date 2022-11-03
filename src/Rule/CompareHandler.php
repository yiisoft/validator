<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use RuntimeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Compares the specified value with another value.
 *
 * The value being compared with {@see Compare::$targetValue} or {@see Compare::$targetAttribute}, which is set
 * in the constructor.
 *
 * It supports different comparison operators, specified
 * via the {@see Compare::$operator}.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to change {@see Compare::$type} to
 * {@see Compare::TYPE_NUMBER} to enable numeric comparison.
 */
final class CompareHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Compare) {
            throw new UnexpectedRuleException(Compare::class, $rule);
        }

        $result = new Result();
        $targetAttribute = $rule->getTargetAttribute();
        $targetValue = $rule->getTargetValue();

        if ($targetValue === null && $targetAttribute !== null) {
            /** @var mixed $targetValue */
            $targetValue = $context->getDataSet()?->getAttributeValue($targetAttribute);
            if (!is_scalar($targetValue)) {
                $message = 'The attribute value returned from a custom data set must have a scalar type.';

                throw new RuntimeException($message);
            }
        }

        if (!$this->compareValues($rule->getOperator(), $rule->getType(), $value, $targetValue)) {
            $parameters = [
                'attribute' => $context->getAttribute(),
                'targetValue' => $rule->getTargetValue(),
                'targetAttribute' => $rule->getTargetAttribute(),
                'targetValueOrAttribute' => $targetValue ?? $targetAttribute,
            ];
            is_scalar($value) ? $parameters['value'] = $value : $parameters['valueType'] = get_debug_type($value);

            $result->addError($rule->getMessage(), $parameters);
        }

        return $result;
    }

    /**
     * Compares two values with the specified operator.
     *
     * @param string $operator The comparison operator.
     * @param string $type The type of the values being compared.
     * @param mixed $value The value being compared.
     * @param mixed $targetValue Another value being compared.
     *
     * @return bool Whether the comparison using the specified operator is true.
     */
    private function compareValues(string $operator, string $type, mixed $value, mixed $targetValue): bool
    {
        if ($type === Compare::TYPE_NUMBER) {
            $value = (float)$value;
            $targetValue = (float)$targetValue;
        } else {
            $value = (string)$value;
            $targetValue = (string)$targetValue;
        }
        return match ($operator) {
            '==' => $value == $targetValue,
            '===' => $value === $targetValue,
            '!=' => $value != $targetValue,
            '!==' => $value !== $targetValue,
            '>' => $value > $targetValue,
            '>=' => $value >= $targetValue,
            '<' => $value < $targetValue,
            '<=' => $value <= $targetValue,
        };
    }
}
