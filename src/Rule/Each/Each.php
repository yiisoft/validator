<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Each;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Each implements RuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var iterable<RuleInterface|RuleInterface[]>
         */
        public iterable $rules = [],
        public string $incorrectInputMessage = 'Value should be array or iterable.',
        public string $message = '{error} {value} given.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof RuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            } else {
                $arrayOfRules[] = [get_class($rule)];
            }
        }
        return $arrayOfRules;
    }
}
