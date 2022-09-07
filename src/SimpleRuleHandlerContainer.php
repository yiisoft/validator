<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Error;
use InvalidArgumentException;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;

use function array_key_exists;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    private array $instances = [];

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!class_exists($className)) {
            throw new RuleHandlerNotFoundException($className);
        }

        if (array_key_exists($className, $this->instances)) {
            return $this->instances[$className];
        }

        try {
            $classInstance = new $className(translator: $this->translator);
        } catch (Error) {
            $classInstance = new $className();
        }

        if (!$classInstance instanceof RuleHandlerInterface) {
            throw new RuleHandlerInterfaceNotImplementedException($className);
        }

        return $this->instances[$className] = $classInstance;
    }
}
