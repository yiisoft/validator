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

        $compoundResult = new Result();
        $results = [];

        foreach ($rule->getRules() as $relatedRule) {
            $rules = [$relatedRule];

            $lastResult = $context->validate($value, $rules);
            $results[] = $lastResult;

            if (!$lastResult->isValid()) {
                break;
            }
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getParameters(), $error->getValuePath());
            }
        }

        return $compoundResult;
    }
}
