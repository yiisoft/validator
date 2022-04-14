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
    public static function getConfigClassName(): string
    {
        return Subset::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_iterable($value)) {
            $result->addError($config->iterableMessage);
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $config->values, $config->strict)) {
            $values = is_array($config->values) ? $config->values : iterator_to_array($config->values);
            $valuesString = '"' . implode('", "', $values) . '"';

            $result->addError($config->subsetMessage, ['values' => $valuesString]);
        }

        return $result;
    }
}
