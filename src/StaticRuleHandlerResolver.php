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

    public function resolve(string $className): RuleHandlerInterface
    {
        return $this->validators[$className] ?? throw new RuleHandlerNotFoundException($className);
    }
}
