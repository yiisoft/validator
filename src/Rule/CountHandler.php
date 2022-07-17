<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Countable;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\FallbackTranslator;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

use function count;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->translator = $translator ?? new FallbackTranslator();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $message = $this->translator->translate(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);

            return $result;
        }

        $count = count($value);

        if ($rule->getExactly() !== null && $count !== $rule->getExactly()) {
            $message = $this->translator->translate(
                $rule->getNotExactlyMessage(),
                ['exactly' => $rule->getExactly(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);

            return $result;
        }

        if ($rule->getMin() !== null && $count < $rule->getMin()) {
            $message = $this->translator->translate(
                $rule->getTooFewItemsMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        if ($rule->getMax() !== null && $count > $rule->getMax()) {
            $message = $this->translator->translate(
                $rule->getTooManyItemsMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        return $result;
    }
}
