<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Psr\Container\ContainerInterface;
use Throwable;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class ContainerRuleHandlerResolver implements RuleHandlerResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(RuleInterface $rule): RuleHandlerInterface
    {
        try {
            return $this->container->get($rule->getHandlerClassName());
        } catch (Throwable $e) {
            throw new RuleHandlerNotFoundException($rule, $e);
        }
    }
}
