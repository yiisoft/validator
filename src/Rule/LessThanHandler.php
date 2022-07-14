<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is less than another value or attribute.
 *
 * The value being validated with {@see LessThan::$targetValue} or {@see LessThan::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are compared byte by byte. When validating numbers, make sure to change {@see LessThan::$type} to
 * {@see LessThan::TYPE_NUMBER} to enable numeric validation.
 */
final class LessThanHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof LessThan) {
            throw new UnexpectedRuleException(LessThan::class, $rule);
        }

        $result = new Result();
        $targetValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isLessThan($value, $targetValue, $rule->getType())) {
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

    private function isLessThan(mixed $value, mixed $targetValue, string $type): bool
    {
        if ($type === LessThan::TYPE_NUMBER) {
            $value = (float)$value;
            $targetValue = (float)$targetValue;
        } else {
            $value = (string)$value;
            $targetValue = (string)$targetValue;
        }

        return $value < $targetValue;
    }
}
