<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_bool;

/**
 * A handler for {@see BooleanType} rule.
 */
final class BooleanTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof BooleanType) {
            throw new UnexpectedRuleException(BooleanType::class, $rule);
        }

        if (!is_bool($value)) {
            return (new Result())->addError($rule->getMessage(), ['attribute' => $context->getTranslatedAttribute()]);
        }

        return new Result();
    }
}
