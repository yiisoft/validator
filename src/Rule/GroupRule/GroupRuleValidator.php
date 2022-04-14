<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\GroupRule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Validates a single value for a set of custom rules.
 */
class GroupRuleValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!$validator->validate($value, $rule->getRuleSet())->isValid()) {
            $result->addError($rule->message);
        }

        return $result;
    }
}
