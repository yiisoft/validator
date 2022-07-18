<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;

final class RuleHandlerContainer implements RuleHandlerResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
