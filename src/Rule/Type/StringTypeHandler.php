<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * A handler for {@see StringType} rule. Validates that the value has string type -
 * {@link https://www.php.net/manual/en/language.types.string.php}.
 */
final class StringTypeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof StringType) {
            throw new UnexpectedRuleException(StringType::class, $rule);
        }

        if (!is_string($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        return new Result();
    }
}
