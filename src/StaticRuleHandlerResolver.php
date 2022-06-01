<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class StaticRuleHandlerResolver implements RuleHandlerResolverInterface
{
    private array $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!in_array($className, $this->handlers, true)) {
            throw new RuleHandlerNotFoundException($className);
        }
        if (!is_subclass_of($className, RuleHandlerInterface::class)) {
            throw new RuleHandlerInterfaceNotImplementedException($className);
        }

        return new $className();
    }
}
