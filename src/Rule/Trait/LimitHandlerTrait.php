<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

trait LimitHandlerTrait
{
    private function validateLimits(
        mixed $value,
        object $rule,
        ValidationContext $context,
        int $number,
        Result $result
    ): void
    {
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
