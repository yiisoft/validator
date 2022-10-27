<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see IsTrue} rule.
 */
final class IsTrueHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof IsTrue) {
            throw new UnexpectedRuleException(IsTrue::class, $rule);
        }

        $valid = $rule->isStrict() ? $value === $rule->getTrueValue() : $value == $rule->getTrueValue();

        $result = new Result();
        if ($valid) {
            return $result;
        }

        /** @psalm-var scalar $value */
        $result->addError(
            $rule->getMessage(),
            [
                'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
                'attribute' => $context->getAttribute(),
                'value' => $value,
            ],
        );

        return $result;
    }
}
