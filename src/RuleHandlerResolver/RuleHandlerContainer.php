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
 * ({@see ContainerInterface}) throwing more specific exceptions and executing additional checks during resolving a rule
 * handler name to make sure that if a handler was found, then it's indeed a valid handler to work with.
 *
 * To use it, make sure to change `config.php` like so:
 *
 * ```php
 * use Yiisoft\Validator\RuleHandlerResolverInterface;
 * use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;
 *
 * [
 *     RuleHandlerResolverInterface::class => RuleHandlerContainer::class,
 * ];
 * ```
 *
 * If you don't need DI container, {@see SimpleRuleHandlerContainer} can be used instead. It's enabled by default, you
 * don't need to additionally configure anything.
 */
final class RuleHandlerContainer implements RuleHandlerResolverInterface
{
    public function __construct(
        /**
         * @var ContainerInterface An instance of dependency injection container.
         */
        private readonly ContainerInterface $container,
    ) {}

    /**
     * Resolves a rule handler name to a corresponding rule handler instance. The actual resolving is delegated to
     * {@see $container}. Throws more specific exceptions and executes additional checks to make sure that if a handler
     * was found, then it's indeed a valid handler to work with.
     *
     * @param string $name A rule handler name ({@see RuleInterface}).
     *
     * @throws RuleHandlerNotFoundException If a rule handler instance was not found.
     * @throws RuleHandlerInterfaceNotImplementedException If a found instance is not a valid rule handler.
     *
     * @return RuleHandlerInterface A corresponding rule handler instance.
     */
    public function resolve(string $name): RuleHandlerInterface
    {
        try {
            $ruleHandler = $this->container->get($name);
        } catch (NotFoundExceptionInterface $e) {
            throw new RuleHandlerNotFoundException($name, previous: $e);
        }

        if (!$ruleHandler instanceof RuleHandlerInterface) {
            throw new RuleHandlerInterfaceNotImplementedException($ruleHandler);
        }

        return $ruleHandler;
    }
}
