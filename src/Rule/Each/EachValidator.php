<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Each;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachValidator implements RuleValidatorInterface
{
    public static function getRuleClassName(): string
    {
        return Each::class;
    }

    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
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
