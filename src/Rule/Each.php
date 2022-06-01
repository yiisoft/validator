<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\RuleInterface;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Each implements ParametrizedRuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var iterable<RuleInterface>
         */
        private iterable $rules = [],
        private string $incorrectInputMessage = 'Value should be array or iterable.',
        private string $message = '{error} {value} given.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
    }

    /**
     * @return iterable<RuleInterface>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    /**
     * @return string
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }

    public function getOptions(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof ParametrizedRuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            } else {
                $arrayOfRules[] = [$rule->getName()];
            }
        }
        return $arrayOfRules;
    }
}
