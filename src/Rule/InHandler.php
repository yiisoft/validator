<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see In} rule.
 */
final class InHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof In) {
            throw new UnexpectedRuleException(In::class, $rule);
        }

        $result = new Result();
        if ($rule->isNot() === ArrayHelper::isIn($value, $rule->getValues(), $rule->isStrict())) {
            $result->addError($rule->getMessage(), ['attribute' => $context->getTranslatedAttribute()]);
        }

        return $result;
    }
}
