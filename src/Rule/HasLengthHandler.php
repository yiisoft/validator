<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
final class HasLengthHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

    public function validate($value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $formattedMessage = $this->formatMessage($rule->getMessage());
            $result->addError($formattedMessage);
            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());

        if ($rule->getMin() !== null && $length < $rule->getMin()) {
            $formattedMessage = $this->formatMessage($rule->getTooShortMessage(), ['min' => $rule->getMin()]);
            $result->addError($formattedMessage);
        }
        if ($rule->getMax() !== null && $length > $rule->getMax()) {
            $formattedMessage = $this->formatMessage($rule->getTooLongMessage(), ['max' => $rule->getMax()]);
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
