<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_float;

/**
 * A handler for {@see FloatType} rule. Validates that the value has float type -
 * {@link https://www.php.net/manual/en/language.types.float.php}.
 */
final class FloatTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof FloatType) {
            throw new UnexpectedRuleException(FloatType::class, $rule);
        }

        if (!is_float($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        return new Result();
    }
}
