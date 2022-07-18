<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
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
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate($value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());

        if ($rule->getMin() !== null && $length < $rule->getMin()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooShortMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }
        if ($rule->getMax() !== null && $length > $rule->getMax()) {
            $formattedMessage = $this->formatter->format(
                $rule->getTooLongMessage(),
                ['max' => $rule->getMax(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
