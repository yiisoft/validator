<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Traversable;
use Yiisoft\Arrays\ArrayHelper;
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
            $result->addError($rule->getIterableMessage(), [
                'attribute' => $context->getAttribute(),
                'valueType' => get_debug_type($value),
            ]);

            return $result;
        }

        if (!ArrayHelper::isSubset($value, $rule->getValues(), $rule->isStrict())) {
            $values = $rule->getValues();
            if ($values instanceof Traversable) {
                $values = iterator_to_array($values);
            }

            $valuesString = '"' . implode('", "', $values) . '"';

            $result->addError(
                $rule->getSubsetMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'values' => $valuesString,
                ],
            );
        }

        return $result;
    }
}
