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

        if ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue();
        } else {
            $valid = $value == $rule->getTrueValue();
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $result->addError(
            message: $rule->getMessage(),
            parameters: [
                'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
                'attribute' => $context->getAttribute(),
                'value' => $value,
            ]
        );

        return $result;
    }
}
