<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_int;

/**
 * A handler for {@see IntegerType} rule. Validates that the value has integer type -
 * {@link https://www.php.net/manual/en/language.types.integer.php}.
 */
final class IntegerTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof IntegerType) {
            throw new UnexpectedRuleException(IntegerType::class, $rule);
        }

        if (!is_int($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        return new Result();
    }
}
