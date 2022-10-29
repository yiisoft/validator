<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\LimitHandlerTrait;
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
    use LimitHandlerTrait;

    public function validate($value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof HasLength) {
            throw new UnexpectedRuleException(HasLength::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getAttribute(),
                'valueType' => get_debug_type($value),
            ]);

            return $result;
        }

        $length = mb_strlen($value, $rule->getEncoding());
        $this->validateLimits($value, $rule, $context, $length, $result);

        return $result;
    }
}
