<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    private array $instances = [];

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!array_key_exists($className, $this->instances)) {
            if (!is_subclass_of($className, RuleHandlerInterface::class)) {
                throw new RuleHandlerInterfaceNotImplementedException($className);
            }
            return $this->instances[$className] = new $className();
        }

        return $this->instances[$className];
    }
}
