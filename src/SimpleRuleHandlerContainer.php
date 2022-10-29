<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;

use function array_key_exists;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    private array $instances = [];

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!class_exists($className)) {
            throw new RuleHandlerNotFoundException($className);
        }

        if (array_key_exists($className, $this->instances)) {
            return $this->instances[$className];
        }

        if (!is_subclass_of($className, RuleHandlerInterface::class)) {
            throw new RuleHandlerInterfaceNotImplementedException($className);
        }

        $classInstance = new $className();

        return $this->instances[$className] = $classInstance;
    }
}
