<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RuleHandlerResolver;

use Psr\Container\ContainerInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

use function array_key_exists;

/**
 * An implementation for {@see RuleHandlerResolverInterface} using internal class instance variable as a storage of rule
 * handlers' instances. Use it if you don't need PSR container ({@see ContainerInterface}), otherwise
 * {@see RuleHandlerContainer} can be used instead. It's enabled by default to you don't need to additionally configure
 * anything.
 *
 * Note that you can predefine handlers by yourself:
 *
 * ```php
 * $container = new SimpleRuleHandlerContainer(['my-handler' => $myHandlerInstance]);
 * $validator = new Validator(ruleHandlerResolver: $container);
 * ```
 *
 * This way `my-handler` can be used as an alias and specified in {@see RuleInterface::getHandler()}.
 *
 * It's also possible to replace a handler for built-in rule with a custom one:
 *
 * ```php
 * $container = new SimpleRuleHandlerContainer([AtLeast::class => new MyAtLeastHandler()]);
 * $validator = new Validator(ruleHandlerResolver: $container);
 * ```
 */
final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    /**
     * @throws RuleHandlerInterfaceNotImplementedException If one of the {@see $instances} is not a valid rule handler.
     */
    public function __construct(
        /**
         * @var array<string, RuleHandlerInterface> A storage of rule handlers' instances - a mapping where keys are
         * strings (the rule handlers' class names by default) and values are corresponding rule handlers' instances.
         */
        private array $instances = [],
    ) {
        foreach ($instances as $instance) {
            if (!$instance instanceof RuleHandlerInterface) {
                throw new RuleHandlerInterfaceNotImplementedException($instance);
            }
        }
    }

    /**
     * Resolves a rule handler name to a corresponding rule handler instance.
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
        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }

        if (!class_exists($name)) {
            throw new RuleHandlerNotFoundException($name);
        }

        if (!is_subclass_of($name, RuleHandlerInterface::class)) {
            throw new RuleHandlerInterfaceNotImplementedException($name);
        }

        $instance = new $name();
        $this->instances[$name] = $instance;

        return $instance;
    }
}
