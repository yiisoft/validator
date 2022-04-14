<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\HasLength;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthValidator implements RuleValidatorInterface
{
    public function validate($value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($rule->message);
            return $result;
        }

        $length = mb_strlen($value, $rule->encoding);

        if ($rule->min !== null && $length < $rule->min) {
            $result->addError($rule->tooShortMessage, ['min' => $rule->min]);
        }
        if ($rule->max !== null && $length > $rule->max) {
            $result->addError($rule->tooLongMessage, ['max' => $rule->max]);
        }

        return $result;
    }
}
