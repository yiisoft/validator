<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\CountableLimitHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value is a string of a certain length.
 *
 * @see Length
 */
final class LengthHandler implements RuleHandlerInterface
{
    use CountableLimitHandlerTrait;

    public function validate($value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Length) {
            throw new UnexpectedRuleException(Length::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => ucfirst($context->getTranslatedAttribute()),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());
        $this->validateCountableLimits($rule, $context, $length, $result);

        return $result;
    }
}
