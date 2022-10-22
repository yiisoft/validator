<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the specified value is passed and not empty.
 */
final class RequiredHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();
        if ($context->isAttributeMissing()) {
            $result->addError($rule->getNotPassedMessage());

            return $result;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if (!$rule->getEmptyCallback()($value, $context->isAttributeMissing())) {
            return $result;
        }

        $result->addError($rule->getMessage());

        return $result;
    }
}
