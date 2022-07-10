<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Compares the specified value with another value.
 *
 * The value being compared with a constant {@see CompareTo::$compareValue}, which is set
 * in the constructor.
 *
 * It supports different comparison operators, specified
 * via the {@see CompareTo::$operator}.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to change {@see CompareTo::$type} to
 * {@see CompareTo::TYPE_NUMBER} to enable numeric comparison.
 */
final class CompareToHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof CompareTo) {
            throw new UnexpectedRuleException(CompareTo::class, $rule);
        }

        $result = new Result();
        $compareAttribute = $rule->getCompareAttribute() ?? $context?->getAttribute() . '_repeat';
        $compareValue = $rule->getCompareValue() ?? $context?->getDataSet()?->getAttributeValue($compareAttribute);

        if (!$this->compareValues($rule->getOperator(), $rule->getType(), $value, $compareValue)) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                [
                    'attribute' => $context?->getAttribute(),
                    'compareValue' => $rule->getCompareValue(),
                    'compareAttribute' => $rule->getCompareAttribute(),
                    'compareValueOrAttribute' => $compareValue ?? $compareAttribute,
                    'value' => $value,
                ]
            );
            $result->addError($formattedMessage);
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
    private function compareValues(string $operator, string $type, mixed $value, mixed $compareValue): bool
    {
        if ($type === CompareTo::TYPE_NUMBER) {
            $value = (float)$value;
            $compareValue = (float)$compareValue;
        } else {
            $value = (string)$value;
            $compareValue = (string)$compareValue;
        }
        return match ($operator) {
            '==' => $value == $compareValue,
            '===' => $value === $compareValue,
            '!=' => $value != $compareValue,
            '!==' => $value !== $compareValue,
            '>' => $value > $compareValue,
            '>=' => $value >= $compareValue,
            '<' => $value < $compareValue,
            '<=' => $value <= $compareValue,
            default => false,
        };
    }
}
