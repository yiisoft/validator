<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class SimpleRuleHandler implements RuleHandlerInterface
{
    public function validate(
        mixed $value,
        object $rule,
        ValidatorInterface $validator,
        ?ValidationContext $context = null
    ): Result {
        if (!$rule instanceof SimpleRule) {
            throw new UnexpectedRuleException(SimpleRule::class, $rule);
        }

        return $rule->validate($value, $context);
    }
}
