<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\InRange;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the rule will ensure the value is NOT among the specified range.
 */
final class InRangeHandler implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof InRange) {
            throw new UnexpectedRuleException(InRange::class, $rule);
        }

        $result = new Result();

        if ($rule->skipOnEmpty && $value === null) {
            return $result;
        }

        if ($rule->not === ArrayHelper::isIn($value, $rule->range, $rule->strict)) {
            $result->addError($rule->message);
        }

        return $result;
    }
}
