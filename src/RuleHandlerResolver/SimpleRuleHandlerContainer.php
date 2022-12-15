<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RuleHandlerResolver;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

use function array_key_exists;

/**
 * An implementation for {@see RuleHandlerResolverInterface} using internal class instance variable as a storage of rule
 * handlers' instances. Use it if you don't need Yii specific configuration ({@see https://github.com/yiisoft/config}),
 * otherwise {@see RuleHandlerContainer} can be added instead. It's enabled by default to you don't need to additionally
 * configure anything.
 */
final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    /**
     * @var array<class-string, RuleHandlerInterface> A storage of rule handlers' instances - a mapping where keys are
     * the rule handlers' class names and values are corresponding rule handlers' instances.
     */
    private array $instances = [];

    /**
     * Resolves a rule handler class name to a corresponding rule handler instance.
     *
     * @param string $className A rule handler class name ({@see RuleInterface}).
     *
     * @return RuleHandlerInterface A corresponding rule handler instance.
     */
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

        $instance = new $className();
        $this->instances[$className] = $instance;

        return $instance;
    }
}
