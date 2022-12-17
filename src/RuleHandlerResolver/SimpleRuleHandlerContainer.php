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
    public function __construct(
        /**
         * @var array<string, RuleHandlerInterface>
         */
        private array $instances = [],
    ) {
        foreach ($instances as $instance) {
            if (!$instance instanceof RuleHandlerInterface) {
                throw new RuleHandlerInterfaceNotImplementedException($instance);
            }
        }
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        if (array_key_exists($className, $this->instances)) {
            return $this->instances[$className];
        }

        if (!class_exists($className)) {
            throw new RuleHandlerNotFoundException($className);
        }

        if (!is_subclass_of($className, RuleHandlerInterface::class)) {
            throw new RuleHandlerInterfaceNotImplementedException($className);
        }

        return $this->instances[$className] = new $className();
    }
}
