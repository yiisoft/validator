<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value is a string.
 *
 * @see StringValue
 */
final class StringValueHandler implements RuleHandlerInterface
{
    public function validate($value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof StringValue) {
            throw new UnexpectedRuleException(StringValue::class, $rule);
        }

        if (!is_string($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'Attribute' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        return new Result();
    }
}
