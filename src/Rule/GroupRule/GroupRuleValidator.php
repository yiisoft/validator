<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\GroupRule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates a single value for a set of custom rules.
 */
class GroupRuleValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return GroupRule::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!$config->getRuleSet()->validate($value, $context)->isValid()) {
            $result->addError($config->message);
        }

        return $result;
    }
}
