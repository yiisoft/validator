<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Any} rule. Validates a set of rules consecutively and stops at the rule where validation
 * has passed.
 */
final class AnyHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Any) {
            throw new UnexpectedRuleException(Any::class, $rule);
        }

        foreach ($rule->getRules() as $relatedRule) {
            $result = $context->validate($value, $relatedRule);
            if ($result->isValid()) {
                return $result;
            }
        }

        return (new Result())->addError($rule->getMessage(), []);
    }
}
