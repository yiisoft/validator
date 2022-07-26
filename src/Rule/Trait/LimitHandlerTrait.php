<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * A trait attachable to a handler of rule with limits.
 */
trait LimitHandlerTrait
{
    /**
     * Runs limits specific validation.
     *
     * @param mixed $value A validated value in its raw form.
     * @param object $rule A rule matching to this handler.
     * @param ValidationContext $context Validation context.
     * @param int $number A validated number to compare with set limits.
     * @param Result $result Result for adding errors.
     *
     * @see LimitTrait for information about limits and messages.
     */
    private function validateLimits(
        mixed $value,
        object $rule,
        ValidationContext $context,
        int $number,
        Result $result
    ): void {
        if ($rule->getExactly() !== null && $number !== $rule->getExactly()) {
            $formattedMessage = $this->formatter->format(
                $rule->getNotExactlyMessage(),
                ['exactly' => $rule->getExactly(), 'attribute' => $context->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return;
        }

        if ($rule->getMin() !== null && $number < $rule->getMin()) {
            $formattedMessage = $this->formatter->format(
                $rule->getLessThanMinMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        if ($rule->getMax() !== null && $number > $rule->getMax()) {
            $formattedMessage = $this->formatter->format(
                $rule->getGreaterThanMaxMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }
    }
}
