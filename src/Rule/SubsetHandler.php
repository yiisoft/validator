<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class SubsetHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Subset) {
            throw new UnexpectedRuleException(Subset::class, $rule);
        }

        $result = new Result();

        if (!is_iterable($value)) {
            $result->addError(
                message: $rule->getIterableMessage(),
                parameters: ['value' => $value]
            );
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $rule->getValues(), $rule->isStrict())) {
            $values = $rule->getValues() instanceof Traversable
                ? iterator_to_array($rule->getValues())
                : $rule->getValues();
            $valuesString = '"' . implode('", "', $values) . '"';

            $result->addError(
                message: $rule->getSubsetMessage(),
                parameters: ['value' => $value, 'values' => $valuesString]
            );
        }

        return $result;
    }
}
