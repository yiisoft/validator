<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the specified value is not equal to another value or attribute.
 *
 * The value being validated with {@see NotEqual::$equalValue} or {@see NotEqual::$equalAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are compared byte by byte. When validating numbers, make sure to change {@see NotEqual::$type} to
 * {@see NotEqual::TYPE_NUMBER} to enable numeric validation.
 */
final class NotEqualHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof NotEqual) {
            throw new UnexpectedRuleException(NotEqual::class, $rule);
        }

        $result = new Result();
        $targetValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isNotEqual($value, $targetValue, $rule->shouldCheckStrictly(), $rule->getType())) {
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

    private function isNotEqual(mixed $value, mixed $targetValue, bool $strict, string $type): bool
    {
        if ($type === Equal::TYPE_NUMBER) {
            $value = (float)$value;
            $targetValue = (float)$targetValue;
        } else {
            $value = (string)$value;
            $targetValue = (string)$targetValue;
        }

        if ($strict) {
            return $value !== $targetValue;
        }

        return $value != $targetValue;
    }
}
