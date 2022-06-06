<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
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
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Number) {
            throw new UnexpectedRuleException(Number::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $formattedMessage = $this->formatter->format(
                $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }

        $pattern = $rule->isAsInteger() ? $rule->getIntegerPattern() : $rule->getNumberPattern();

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $formattedMessage = $this->formatter->format(
                $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        } elseif ($rule->getMin() !== null && $value < $rule->getMin()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooSmallMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        } elseif ($rule->getMax() !== null && $value > $rule->getMax()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooBigMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
