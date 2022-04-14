<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\AtLeast;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
final class AtLeastValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return AtLeast::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $filledCount = 0;

        foreach ($config->attributes as $attribute) {
            if (!$this->isEmpty($value->{$attribute})) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $config->min) {
            $result->addError($config->message, ['min' => $config->min]);
        }

        return $result;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     *
     * @param mixed $value the value to be checked
     *
     * @return bool whether the value is empty
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
