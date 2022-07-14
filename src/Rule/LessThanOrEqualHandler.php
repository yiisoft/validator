<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is less than or equal to another value or attribute.
 *
 * The value being validated with {@see LessThanOrEqual::$targetValue} or {@see LessThanOrEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are compared byte by byte. When validating numbers, make sure to change {@see LessThanOrEqual::$type} to
 * {@see LessThanOrEqual::TYPE_NUMBER} to enable numeric validation.
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
            throw new UnexpectedRuleException(LessThanOrEqual::class, $rule);
        }

        $result = new Result();
        $expectedValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isLessThanOrEqual($value, $expectedValue, $rule->getType())) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                [
                    'attribute' => $context?->getAttribute(),
                    'targetAttribute' => $rule->getTargetValue(),
                    'targetValue' => $rule->getTargetValue(),
                    'targetValueOrAttribute' => $rule->getTargetValue() ?? $rule->getTargetAttribute(),
                    'value' => $value,
                ]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }

    private function isLessThanOrEqual(mixed $value, mixed $expectedValue, string $type): bool
    {
        if ($type === LessThanOrEqual::TYPE_NUMBER) {
            $value = (float)$value;
            $expectedValue = (float)$expectedValue;
        } else {
            $value = (string)$value;
            $expectedValue = (string)$expectedValue;
        }

        return $value <= $expectedValue;
    }
}
