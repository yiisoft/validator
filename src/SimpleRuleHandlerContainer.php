<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    private array $instances = [];

    /**
     * @param array<class-string> $handlers List of rule handler classes.
     */
    public function __construct(private array $handlers = [])
    {
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!array_key_exists($className, $this->instances)) {
            if (!in_array($className, $this->handlers, true)) {
                throw new RuleHandlerNotFoundException($className);
            }
            if (!is_subclass_of($className, RuleHandlerInterface::class)) {
                throw new RuleHandlerInterfaceNotImplementedException($className);
            }
            return $this->instances[$className] = new $className();
        }

        return $this->instances[$className];
    }
}
