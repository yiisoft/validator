<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Countable;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use function count;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $formattedMessage = $this->formatMessage(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        $count = count($value);

        if ($rule->getExactly() !== null && $count !== $rule->getExactly()) {
            $formattedMessage = $this->formatMessage(
                $rule->getNotExactlyMessage(),
                ['exactly' => $rule->getExactly(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        if ($rule->getMin() !== null && $count < $rule->getMin()) {
            $formattedMessage = $this->formatMessage(
                $rule->getTooFewItemsMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        if ($rule->getMax() !== null && $count > $rule->getMax()) {
            $formattedMessage = $this->formatMessage(
                $rule->getTooManyItemsMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
