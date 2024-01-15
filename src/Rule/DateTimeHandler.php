<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use DateTimeInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

use function is_string;

/**
 * Validates that the value is a valid date.
 */
final class DateTimeHandler implements RuleHandlerInterface
{
    private string $format = DateTimeInterface::W3C;

    /**
     * Validates a value against a DateTime rule.
     *
     * @param mixed $value The value to validate.
     * @param object $rule The DateTime rule.
     * @param ValidationContext $context The validation context.
     * @return Result The validation result.
     * @throws UnexpectedRuleException If the rule is not an instance of DateTime.
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof DateTime) {
            throw new UnexpectedRuleException(DateTime::class, $rule);
        }
        $result = new Result();
        $datetime = $this->toDateTime($value, $rule);

        $debugType = get_debug_type($value);

        if ($datetime === null) {
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $debugType,
            ]);
        }

        if ($rule->getMin() && $datetime < $rule->getMin()) {
            return $result->addError($rule->getLessThanMinMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $debugType,
                'min' => $rule->getMin()->format($this->format),
            ]);
        }

        if ($rule->getMax() && $datetime > $rule->getMax()) {
            return $result->addError($rule->getGreaterThanMaxMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $debugType,
                'max' => $rule->getMax()->format($this->format),
            ]);
        }

        return $result;
    }


    /**
     * Converts a value to a DateTime object based on a given rule.
     *
     * @param mixed $value The value to convert.
     * @param DateTime $rule The DateTime rule to use for conversion.
     * @return DateTimeInterface|null The converted DateTime object, or null if the conversion fails.
     */
    private function toDateTime(mixed $value, DateTime $rule): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            $this->format = 'U';
            if ($value = \DateTime::createFromFormat($this->format, (string) $value)) {
                return $value;
            }
            return null;
        }

        if (!is_string($value)) {
            return null;
        }
        // Try to convert the value to a DateTime object using each format in the rule's formats array.
        foreach ($rule->getFormats() as $format) {
            if ($formatted = \DateTime::createFromFormat($format, $value)) {
                $this->format = $format;
                return $formatted;
            }
        }

        return null;
    }
}
