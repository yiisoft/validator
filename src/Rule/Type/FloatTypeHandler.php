<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_float;

/**
 * A handler for {@see FloatType} rule.
 */
final class FloatTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof FloatType) {
            throw new UnexpectedRuleException(FloatType::class, $rule);
        }

        if (!is_float($value)) {
            return (new Result())->addError($rule->getMessage(), ['attribute' => $context->getTranslatedAttribute()]);
        }

        return new Result();
    }
}
