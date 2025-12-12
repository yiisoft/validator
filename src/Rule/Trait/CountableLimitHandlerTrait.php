<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use Yiisoft\Validator\CountableLimitInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A trait attachable to a handler of rule with limits. Requires a rule to implement {@LimitInterface} / include
 * {@see CountableLimitTrait}.
 */
trait CountableLimitHandlerTrait
{
    /**
     * Runs countable limits specific validation.
     *
     * @param CountableLimitInterface|RuleInterface $rule A rule matching to this handler.
     * @param ValidationContext $context Validation context.
     * @param int $number A validated number to compare with set limits.
     * @param Result $result Result for adding errors.
     *
     * @see CountableLimitTrait for information about limits and messages.
     */
    private function validateCountableLimits(
        CountableLimitInterface|RuleInterface $rule,
        ValidationContext $context,
        int $number,
        Result $result,
    ): void {
        if (!$rule instanceof CountableLimitInterface || !$rule instanceof RuleInterface) {
            throw new InvalidArgumentException('$rule must implement both LimitInterface and RuleInterface.');
        }

        /**
         * @var CountableLimitTrait|RuleInterface $rule
         *
         * @psalm-ignore-var
         */
        if ($rule->getExactly() !== null && $number !== $rule->getExactly()) {
            $result->addError($rule->getNotExactlyMessage(), [
                'exactly' => $rule->getExactly(),
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'number' => $number,
            ]);

            return;
        }

        if ($rule->getMin() !== null && $number < $rule->getMin()) {
            $result->addError($rule->getLessThanMinMessage(), [
                'min' => $rule->getMin(),
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'number' => $number,
            ]);
        }

        if ($rule->getMax() !== null && $number > $rule->getMax()) {
            $result->addError($rule->getGreaterThanMaxMessage(), [
                'max' => $rule->getMax(),
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'number' => $number,
            ]);
        }
    }
}
