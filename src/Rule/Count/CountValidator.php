<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Count;

use Countable;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function count;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountValidator implements RuleValidatorInterface
{
    public static function getRuleClassName(): string
    {
        return Count::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_countable($value)) {
            $result->addError($config->message);

            return $result;
        }

        $count = count($value);

        if ($config->exactly !== null && $count !== $config->exactly) {
            $result->addError($config->notExactlyMessage, ['exactly' => $config->exactly]);

            return $result;
        }

        if ($config->min !== null && $count < $config->min) {
            $result->addError($config->tooFewItemsMessage, ['min' => $config->min]);
        }

        if ($config->max !== null && $count > $config->max) {
            $result->addError($config->tooManyItemsMessage, ['max' => $config->max]);
        }

        return $result;
    }
}
