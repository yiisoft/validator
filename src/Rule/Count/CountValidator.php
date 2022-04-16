<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Count;

use Countable;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function count;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $result->addError($rule->message);

            return $result;
        }

        $count = count($value);

        if ($rule->exactly !== null && $count !== $rule->exactly) {
            $result->addError($rule->notExactlyMessage, ['exactly' => $rule->exactly]);

            return $result;
        }

        if ($rule->min !== null && $count < $rule->min) {
            $result->addError($rule->tooFewItemsMessage, ['min' => $rule->min]);
        }

        if ($rule->max !== null && $count > $rule->max) {
            $result->addError($rule->tooManyItemsMessage, ['max' => $rule->max]);
        }

        return $result;
    }
}
