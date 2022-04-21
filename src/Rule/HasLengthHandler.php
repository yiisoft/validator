<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthHandler implements RuleHandlerInterface
{
    public function validate($value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

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
