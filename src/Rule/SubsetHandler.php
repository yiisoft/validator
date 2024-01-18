<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Subset} rule. Validates that the set of values is a subset of another set.
 */
final class SubsetHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Subset) {
            throw new UnexpectedRuleException(Subset::class, $rule);
        }

        if (!is_iterable($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => StringHelper::uppercaseFirstCharacter($context->getTranslatedAttribute()),
                'type' => get_debug_type($value),
            ]);
        }

        if (!ArrayHelper::isSubset($value, $rule->getValues(), $rule->isStrict())) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => StringHelper::uppercaseFirstCharacter($context->getTranslatedAttribute()),
            ]);
        }

        return new Result();
    }
}
