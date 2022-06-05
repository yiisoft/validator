<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\PreValidatableRuleInterface;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\Rule\Trait\PreValidatableTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\RulesDumper;

/**
 * Validates a single value for a set of custom rules.
 */
abstract class GroupRule implements ParametrizedRuleInterface, PreValidatableRuleInterface
{
    use HandlerClassNameTrait;
    use PreValidatableTrait;
    use RuleNameTrait;

    public function __construct(
        private string $message = 'This value is not a valid.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Return custom rules set
     */
    abstract public function getRuleSet(): array;

    public function getOptions(): array
    {
        return (new RulesDumper())->asArray($this->getRuleSet());
    }
}
