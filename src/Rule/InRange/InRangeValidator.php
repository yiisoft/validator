<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\InRange;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Validates that the value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the rule will ensure the value is NOT among the specified range.
 */
final class InRangeValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return InRange::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if ($config->skipOnEmpty && $value === null) {
            return $result;
        }

        if ($config->not === ArrayHelper::isIn($value, $config->range, $config->strict)) {
            $result->addError($config->message);
        }

        return $result;
    }
}
