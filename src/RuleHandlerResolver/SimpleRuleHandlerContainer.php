<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RuleHandlerResolver;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

use function array_key_exists;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    /**
     * @var array<class-string, RuleHandlerInterface>
     */
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

        return $this->instances[$className] = new $className();
    }
}
