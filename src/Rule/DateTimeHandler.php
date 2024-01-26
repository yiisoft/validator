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

        if ((!is_string($value) && !is_int($value) && !is_float($value)) || empty($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }
        \DateTime::createFromFormat($rule->getFormat(), (string)$value);

        // Before PHP 8.2 may return array instead of false (see https://github.com/php/php-src/issues/9431).
        $errors = \DateTime::getLastErrors() ?: [ 'error_count' => 0, 'warning_count' => 0 ];
        if ($errors['error_count'] !== 0  ||  $errors['warning_count'] !== 0) {
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }
}
