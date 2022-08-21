<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;

final class SimpleRuleHandlerContainer implements RuleHandlerResolverInterface
{
    private array $instances = [];

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function resolve(string $className): RuleHandlerInterface
    {
        if (!class_exists($className)) {
            throw new RuleHandlerNotFoundException($className);
        }
        if (!array_key_exists($className, $this->instances)) {
            $classInstance = new $className($this->translator);
            if (!$classInstance instanceof RuleHandlerInterface) {
                throw new RuleHandlerInterfaceNotImplementedException($className);
            }
            return $this->instances[$className] = $classInstance;
        }

        return $this->instances[$className];
    }
}
