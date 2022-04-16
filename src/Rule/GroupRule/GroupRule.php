<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\GroupRule;

use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;

/**
 * Validates a single value for a set of custom rules.
 */
abstract class GroupRule implements RuleInterface
{
    use RuleNameTrait;
    use HandlerClassNameTrait;

    public function __construct(
        public string $message = 'This value is not a valid.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    /**
     * Return custom rules set
     */
    abstract public function getRuleSet(): array;

    public function getOptions(): array
    {
        $dumper = new RulesDumper();

        return $dumper->asArray($this->getRuleSet(), true);
    }
}
