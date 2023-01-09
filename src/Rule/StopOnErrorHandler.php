<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see StopOnError} rule. Validates a set of rules consecutively and stops at the rule where validation
 * has failed.
 */
final class StopOnErrorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof StopOnError) {
            throw new UnexpectedRuleException(StopOnError::class, $rule);
        }

        if ($context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return new Result();
        }

        foreach ($rule->getRules() as $relatedRule) {
            $result = $context->validate($value, $relatedRule);
            if (!$result->isValid()) {
                return $result;
            }
        }

        return new Result();
    }
}
