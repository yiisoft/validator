<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        $rules = $rule->getRules();
        if ($rules === []) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError(
                $rule->getIncorrectInputMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );

            return $result;
        }

        foreach ($value as $index => $item) {
            /** @var array<mixed, Closure|Closure[]|RuleInterface|RuleInterface[]> $rule */
            $rule = [$index => $rules];
            $itemResult = $context->getValidator()->validate($item, $rule);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->addError(
                    $error->getMessage(),
                    $error->getParameters(),
                    $error->getValuePath() === []
                        ? [$index]
                        : [$index, ...$error->getValuePath()],
                );
            }
        }

        return $result;
    }
}
