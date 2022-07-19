<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate($value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $message = $this->translator->translate(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());

        if ($rule->getMin() !== null && $length < $rule->getMin()) {
            $message = $this->translator->translate(
                $rule->getTooShortMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }
        if ($rule->getMax() !== null && $length > $rule->getMax()) {
            $message = $this->translator->translate(
                $rule->getTooLongMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        return $result;
    }
}
