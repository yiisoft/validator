<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Each;

use Attribute;
use Closure;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\RuleSet;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Each implements ParametrizedRuleInterface
{
    use RuleNameTrait;

    public ?RuleSet $ruleSet = null;

    public function __construct(
        public iterable $rules = [],
        public string $incorrectInputMessage = 'Value should be array or iterable.',
        public string $message = '{error} {value} given.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
//        if ($rules !== []) {
        $this->ruleSet = new RuleSet($rules);
//        }
    }

    public function getOptions(): array
    {
        return $this->ruleSet->asArray();
    }
}
