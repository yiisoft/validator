<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is equal to another value or attribute.
 *
 * The value being validated with a constant {@see Equal::$equalValue} or attribute {@see Equal::$equalAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are compared byte by byte. When validating numbers, make sure to change {@see Equal::$type} to
 * {@see Equal::TYPE_NUMBER} to enable numeric validation.
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
        $targetValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isEquals($value, $targetValue, $rule->shouldCheckStrictly(), $rule->getType())) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                [
                    'attribute' => $context?->getAttribute(),
                    'targetAttribute' => $rule->getTargetAttribute(),
                    'targetValue' => $rule->getTargetValue(),
                    'targetValueOrAttribute' => $rule->getTargetValue() ?? $rule->getTargetAttribute(),
                    'value' => $value,
                ]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }

    private function isEquals(mixed $value, mixed $targetValue, bool $strict, string $type): bool
    {
        if ($type === Equal::TYPE_NUMBER) {
            $value = (float)$value;
            $targetValue = (float)$targetValue;
        } else {
            $value = (string)$value;
            $targetValue = (string)$targetValue;
        }

        if ($strict) {
            return $value === $targetValue;
        }

        return $value == $targetValue;
    }
}
