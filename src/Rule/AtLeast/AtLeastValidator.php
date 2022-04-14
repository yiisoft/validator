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

    public static function getRuleClassName(): string
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
}
