<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        /**
         * @var iterable<RuleInterface> $rules
         */
        $rules = $rule->rules;
        if ($rules === []) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($rule->incorrectInputMessage);

            return $result;
        }

        foreach ($value as $index => $item) {
            /**
             * @psalm-suppress InvalidArgument
             */
            $itemResult = $context->getValidator()->validate($item, [$index => $rules]);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->mergeError($error);
            }
        }

        return $result;
    }
}
