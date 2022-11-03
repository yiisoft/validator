<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use Yiisoft\Validator\LimitInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A trait attachable to a handler of rule with limits.
 */
trait LimitHandlerTrait
{
    /**
     * Runs limits specific validation.
     *
     * @param LimitInterface|RuleInterface $rule A rule matching to this handler.
     * @param ValidationContext $context Validation context.
     * @param int $number A validated number to compare with set limits.
     * @param Result $result Result for adding errors.
     *
     * @see LimitTrait for information about limits and messages.
     */
    private function validateLimits(
        LimitInterface|RuleInterface $rule,
        ValidationContext $context,
        int $number,
        Result $result
    ): void {
        if (!$rule instanceof LimitInterface || !$rule instanceof RuleInterface) {
            throw new InvalidArgumentException('$rule must implement bith LimitInterface and RuleInterface.');
        }

        /**
         * @var LimitTrait|RuleInterface $rule
         * @psalm-ignore-var
         */
        if ($rule->getExactly() !== null && $number !== $rule->getExactly()) {
            $result->addError($rule->getNotExactlyMessage(), [
                'exactly' => $rule->getExactly(),
                'attribute' => $context->getAttribute(),
            ]);

            return;
        }

        if ($rule->getMin() !== null && $number < $rule->getMin()) {
            $result->addError($rule->getLessThanMinMessage(), [
                'min' => $rule->getMin(),
                'attribute' => $context->getAttribute(),
            ]);
        }

        if ($rule->getMax() !== null && $number > $rule->getMax()) {
            $result->addError($rule->getGreaterThanMaxMessage(), [
                'max' => $rule->getMax(),
                'attribute' => $context->getAttribute(),
            ]);
        }
    }
}
