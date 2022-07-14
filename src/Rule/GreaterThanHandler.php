<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is greater than another value or attribute.
 *
 * The value being validated with {@see GreaterThan::$targetValue} or {@see GreaterThan::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are compared byte by byte. When validating numbers, make sure to change {@see GreaterThan::$type} to
 * {@see GreaterThan::TYPE_NUMBER} to enable numeric validation.
 */
final class GreaterThanHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof GreaterThan) {
            throw new UnexpectedRuleException(GreaterThan::class, $rule);
        }

        $result = new Result();
        $targetValue = $rule->getTargetValue() ?? $context?->getDataSet()?->getAttributeValue($rule->getTargetAttribute());

        if (!$this->isGreaterThan($value, $targetValue, $rule->getType())) {
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

    private function isGreaterThan(mixed $value, mixed $targetValue, string $type): bool
    {
        if ($type === GreaterThan::TYPE_NUMBER) {
            $value = (float)$value;
            $targetValue = (float)$targetValue;
        } else {
            $value = (string)$value;
            $targetValue = (string)$targetValue;
        }

        return $value > $targetValue;
    }
}
