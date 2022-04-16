<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Each;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        if ($rule->rules === null) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($rule->incorrectInputMessage);

            return $result;
        }

        foreach ($value as $index => $item) {
            $itemResult = $validator->validate($item, [$index => $rule->rules]);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->merge($error);
            }
        }

        return $result;
    }
}
