<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_int;
use function is_string;

/**
 * A handler for {@see Each} rule. Validates each element of an iterable using a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        /** @var mixed $value */
        $value = $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY) ?? $value;
        if (!is_iterable($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $rules = $rule->getRules();
        $result = new Result();

        $originalEachKey = $context->getParameter(Each::PARAMETER_EACH_KEY);

        /** @var mixed $item */
        foreach ($value as $index => $item) {
            if (!is_int($index) && !is_string($index)) {
                return (new Result())->addError($rule->getIncorrectInputKeyMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                    'type' => get_debug_type($value),
                ]);
            }

            $context->setParameter(Each::PARAMETER_EACH_KEY, $index);

            $itemResult = $context->validate($item, $rules);
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

        $context->setParameter(Each::PARAMETER_EACH_KEY, $originalEachKey);

        return $result;
    }
}
