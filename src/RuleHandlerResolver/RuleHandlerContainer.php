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
    public function __construct(
        private ContainerInterface $container,
    )
    {
    }

    public function resolve(string $ruleClassName): RuleHandlerInterface
    {
        try {
            $ruleHandler = $this->container->get($ruleClassName);
        } catch (NotFoundExceptionInterface $e) {
            throw new RuleHandlerNotFoundException($ruleClassName, $e);
        }

        if (!$ruleHandler instanceof RuleHandlerInterface) {
            throw new RuleHandlerInterfaceNotImplementedException($ruleClassName);
        }

        return $ruleHandler;
    }
}
