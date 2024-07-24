<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_bool;

/**
 * A handler for {@see BooleanType} rule. Validates that the value has a boolean type (strictly) -
 * {@link https://www.php.net/manual/en/language.types.boolean.php}.
 */
final class BooleanTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof BooleanType) {
            throw new UnexpectedRuleException(BooleanType::class, $rule);
        }

        if (!is_bool($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        return new Result();
    }
}
