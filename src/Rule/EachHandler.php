<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_int;
use function is_string;

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

        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getAttribute(),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        /** @var mixed $item */
        foreach ($value as $index => $item) {
            if (!is_int($index) && !is_string($index)) {
                $result->addError($rule->getIncorrectInputKeyMessage(), [
                    'attribute' => $context->getAttribute(),
                    'type' => get_debug_type($value),
                ]);

                return $result;
            }

            $itemResult = $context->getValidator()->validate($item, $rules);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->addError(
                    $error->getMessage(),
                    $error->getParameters(),
                    $error->getValuePath() === [] ? [$index] : [$index, ...$error->getValuePath()],
                );
            }
        }

        return $result;
    }
}
