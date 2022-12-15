<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RuleHandlerResolver;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

/**
 * An implementation for {@see RuleHandlerResolverInterface} acting as a wrapper over dependency injection container
 * ({@see ContainerInterface}) throwing more specific exceptions and executing some additional checks (to make sure that
 * if a handler was found, then it's indeed a valid handler to work with) during resolving a rule handler class name.
 */
final class RuleHandlerContainer implements RuleHandlerResolverInterface
{
    public function __construct(
        /**
         * @var ContainerInterface An instance of dependency injection container.
         */
        private ContainerInterface $container,
    ) {
    }

    /**
     * Resolves a rule handler class name to a corresponding rule handler instance. The actual resolving is delegated to
     * {@see $container}. Throws more specific exceptions and executes some additional checks (to make sure that if a
     * handler was found, then it's indeed a valid handler to work with).
     *
     * @param string $className A rule handler class name ({@see RuleInterface}).
     *
     * @throws RuleHandlerNotFoundException if a rule handler instance was not found.
     * @throws RuleHandlerInterfaceNotImplementedException if a found instance is not a valid rule handler.
     *
     * @return RuleHandlerInterface A corresponding rule handler instance.
     */
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
