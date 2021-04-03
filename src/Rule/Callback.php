<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Psr\Container\ContainerInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Validator\Exception\InvalidCallbackRuleConfiguration;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

class Callback extends Rule
{
    /**
     * @var callable
     */
    private $callback;
    private ?ContainerInterface $container = null;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $callback = $this->callback;
        if ($this->isInjectorAvailable()) {
            if ($this->container === null) {
                throw new \RuntimeException('You should add container to rule via withContainer() method.');
            }
            $callbackResult = (new Injector($this->container))->invoke($this->callback, [$value, $context]);
        } else {
            $callbackResult = $callback($value, $context);
        }

        if (!$callbackResult instanceof Result) {
            throw new CallbackRuleException($callbackResult);
        }

        $result = new Result();

        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->formatMessage($message));
            }
        }
        return $result;
    }

    /**
     * @throws InvalidCallbackRuleConfiguration
     */
    public function withContainer(ContainerInterface $container): self
    {
        if (!$this->isInjectorAvailable()) {
            throw new InvalidCallbackRuleConfiguration();
        }
        $new = clone $this;
        $new->container = $container;
        return $new;
    }

    private function isInjectorAvailable(): bool
    {
        return class_exists(Injector::class);
    }
}
