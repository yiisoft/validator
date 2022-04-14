<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Subset;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

final class SubsetValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_iterable($value)) {
            $result->addError($rule->iterableMessage);
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $rule->values, $rule->strict)) {
            $values = is_array($rule->values) ? $rule->values : iterator_to_array($rule->values);
            $valuesString = '"' . implode('", "', $values) . '"';

            $result->addError($rule->subsetMessage, ['values' => $valuesString]);
        }

        return $result;
    }
}
