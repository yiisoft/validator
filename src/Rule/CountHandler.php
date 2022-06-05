<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Countable;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

use function count;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountHandler implements RuleHandlerInterface
{
    private ?FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        $count = count($value);

        if ($rule->getExactly() !== null && $count !== $rule->getExactly()) {
            $formattedMessage = $this->formatter->format(
                $rule->getNotExactlyMessage(),
                ['exactly' => $rule->getExactly(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        if ($rule->getMin() !== null && $count < $rule->getMin()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooFewItemsMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        if ($rule->getMax() !== null && $count > $rule->getMax()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooManyItemsMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
