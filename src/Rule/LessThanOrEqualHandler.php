<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the specified value is less than another value or attribute.
 *
 * The value being checked with a constant {@see Equal::$equalValue} or attribute {@see Equal::$equalAttribute}, which
 * is set in the constructor.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When checking numbers, make sure to change {@see LessThan::$type} to
 * {@see LessThan::TYPE_NUMBER} to enable numeric comparison.
 */
final class LessThanOrEqualHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof LessThanOrEqual) {
            throw new UnexpectedRuleException(Equal::class, $rule);
        }

        $result = new Result();
        $expectedValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isEquals($value, $expectedValue, $rule->getType())) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                [
                    'attribute' => $context?->getAttribute(),
                    'equalAttribute' => $rule->getTargetValue(),
                    'equalValue' => $rule->getTargetValue(),
                    'equalValueOrAttribute' => $rule->getTargetValue() ?? $rule->getTargetAttribute(),
                    'value' => $value,
                ]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }

    private function isEquals(mixed $value, mixed $expectedValue, string $type): bool
    {
        if ($type === Equal::TYPE_NUMBER) {
            $value = (float)$value;
            $expectedValue = (float)$expectedValue;
        } else {
            $value = (string)$value;
            $expectedValue = (string)$expectedValue;
        }

        return $value <= $expectedValue;
    }
}
