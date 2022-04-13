<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\GroupRule;

use Closure;
use Yiisoft\Validator\RuleSet;

/**
 * Validates a single value for a set of custom rules.
 */
abstract class GroupRule
{
    public function __construct(
        public string   $message = 'This value is not a valid.',
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    ) {

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
