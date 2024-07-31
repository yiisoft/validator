<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function in_array;

/**
 * Validates that the value is one of the values of a specified enum.
 *
 * @see InEnum
 */
final class InEnumHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof InEnum) {
            throw new UnexpectedRuleException(InEnum::class, $rule);
        }

        $result = new Result();
        if ($rule->isNot() === in_array($value, $rule->getValues(), $rule->isStrict())) {
            $result->addError(
                $rule->getMessage(),
                [
                    'property' => $context->getTranslatedProperty(),
                    'Property' => $context->getCapitalizedTranslatedProperty(),
                ],
            );
        }

        return $result;
    }
}
