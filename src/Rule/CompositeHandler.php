<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Composite} handler. Validates group of rules.
 */
final class CompositeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Composite) {
            throw new UnexpectedRuleException(Composite::class, $rule);
        }

        return $context->validate($value, $rule->getRules());
    }
}
