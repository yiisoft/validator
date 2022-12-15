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

    public function resolve(string $ruleClassName): RuleHandlerInterface
    {
        if (!class_exists($ruleClassName)) {
            throw new RuleHandlerNotFoundException($ruleClassName);
        }

        if (array_key_exists($ruleClassName, $this->instances)) {
            return $this->instances[$ruleClassName];
        }

        if (!is_subclass_of($ruleClassName, RuleHandlerInterface::class)) {
            throw new RuleHandlerInterfaceNotImplementedException($ruleClassName);
        }

        $handler = new $ruleClassName();
        $this->instances[$ruleClassName] = $handler;

        return $handler;
    }
}
