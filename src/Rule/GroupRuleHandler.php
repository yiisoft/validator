<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates a single value for a set of custom rules.
 */
class GroupRuleHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof GroupRule) {
            throw new UnexpectedRuleException(GroupRule::class, $rule);
        }

        $result = new Result();
        if (!$context->getValidator()->validate($value, $rule->getRuleSet())->isValid()) {
            $result->addError($rule->message);
        }

        return $result;
    }
}
