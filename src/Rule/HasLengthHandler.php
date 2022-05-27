<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthHandler implements RuleHandlerInterface
{
    public function validate($value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $result->addError($rule->getMessage());
            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());

        if ($rule->getMin() !== null && $length < $rule->getMin()) {
            $result->addError($rule->getTooShortMessage(), ['min' => $rule->getMin()]);
        }
        if ($rule->getMax() !== null && $length > $rule->getMax()) {
            $result->addError($rule->getTooLongMessage(), ['max' => $rule->getMax()]);
        }

        return $result;
    }
}
