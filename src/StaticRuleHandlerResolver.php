<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class StaticRuleHandlerResolver implements RuleHandlerResolverInterface
{
    private array $validators;

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    public function resolve(RuleInterface $rule): RuleHandlerInterface
    {
        return $this->validators[$rule->getHandlerClassName()] ?? throw new RuleHandlerNotFoundException($rule);
    }
}
