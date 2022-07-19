<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
final class NumberHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Number) {
            throw new UnexpectedRuleException(Number::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $message = $this->translator->translate(
                $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
            return $result;
        }

        $pattern = $rule->isAsInteger() ? $rule->getIntegerPattern() : $rule->getNumberPattern();

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $message = $this->translator->translate(
                $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        } elseif ($rule->getMin() !== null && $value < $rule->getMin()) {
            $message = $this->translator->translate(
                $rule->getTooSmallMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        } elseif ($rule->getMax() !== null && $value > $rule->getMax()) {
            $message = $this->translator->translate(
                $rule->getTooBigMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        return $result;
    }
}
