<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\GroupRule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates a single value for a set of custom rules.
 */
abstract class GroupRuleValidator
{
    public static function getConfigClassName(): string
    {
        return GroupRule::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!$this->getRuleSet()->validate($value, $context)->isValid()) {
            $result->addError($config->message);
        }

        return $result;
    }

    /**
     * Return custom rules set
     */
    abstract protected function getRuleSet(): RuleSet;

    public function getOptions(): array
    {
        return $this->getRuleSet()->asArray();
    }
}
