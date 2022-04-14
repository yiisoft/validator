<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\AtLeast;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\EmptyCheckTrait;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
final class AtLeastValidator implements RuleValidatorInterface
{
    use EmptyCheckTrait;

    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $filledCount = 0;

        foreach ($rule->attributes as $attribute) {
            if (!$this->isEmpty($value->{$attribute})) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $rule->min) {
            $result->addError($rule->message, ['min' => $rule->min]);
        }

        return $result;
    }
}
