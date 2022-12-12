<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RuleHandlerResolver;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

final class RuleHandlerContainer implements RuleHandlerResolverInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        try {
            $ruleHandler = $this->container->get($className);
        } catch (NotFoundExceptionInterface $e) {
            throw new RuleHandlerNotFoundException($className, $e);
        }

        if (!$ruleHandler instanceof RuleHandlerInterface) {
            throw new RuleHandlerInterfaceNotImplementedException($className);
        }

        return $ruleHandler;
    }
}
