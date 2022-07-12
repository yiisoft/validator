<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Equals the specified value with another value.
 *
 * The value being compared with a constant {@see Equal::$value}, which is set
 * in the constructor.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to change {@see CompareTo::$type} to
 * {@see CompareTo::TYPE_NUMBER} to enable numeric comparison.
 */
final class EqualHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Equal) {
            throw new UnexpectedRuleException(Equal::class, $rule);
        }

        $result = new Result();
        $expectedValue = $rule->getEqualValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getEqualAttribute());

        if (!$this->isEquals($value, $expectedValue, $rule->shouldCheckStrictly(), $rule->getType())) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                [
                    'attribute' => $context?->getAttribute(),
                    'equalAttribute' => $rule->getEqualAttribute(),
                    'equalValue' => $rule->getEqualValue(),
                    'equalValueOrAttribute' => $rule->getEqualValue() ?? $rule->getEqualAttribute(),
                    'value' => $value,
                ]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }

    private function isEquals(mixed $value, mixed $expectedValue, bool $strict, string $type): bool
    {
        if ($type === Equal::TYPE_NUMBER) {
            $value = (float)$value;
            $expectedValue = (float)$expectedValue;
        } else {
            $value = (string)$value;
            $expectedValue = (string)$expectedValue;
        }

        if ($strict) {
            return $value === $expectedValue;
        }

        return $value == $expectedValue;
    }
}
