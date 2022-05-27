<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Countable;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use function count;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $result->addError($rule->getMessage());

            return $result;
        }

        $count = count($value);

        if ($rule->getExactly() !== null && $count !== $rule->getExactly()) {
            $result->addError($rule->getNotExactlyMessage(), ['exactly' => $rule->getExactly()]);

            return $result;
        }

        if ($rule->getMin() !== null && $count < $rule->getMin()) {
            $result->addError($rule->getTooFewItemsMessage(), ['min' => $rule->getMin()]);
        }

        if ($rule->getMax() !== null && $count > $rule->getMax()) {
            $result->addError($rule->getTooManyItemsMessage(), ['max' => $rule->getMax()]);
        }

        return $result;
    }
}
