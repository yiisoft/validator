<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

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
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof DateTime) {
            throw new UnexpectedRuleException(DateTime::class, $rule);
        }

        $result = new Result();

        if (!is_string($value) || empty($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $date = \DateTime::createFromFormat($rule->getFormat(), $value);

        if ($date === false  || ($date->format($rule->getFormat()) !== $value)) {
             $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }
}
