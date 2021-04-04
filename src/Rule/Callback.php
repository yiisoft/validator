<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Injector\Injector;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

class Callback extends Rule
{
    /**
     * @var callable
     */
    private $callback;
    private ?Injector $injector = null;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $callback = $this->callback;
        $callbackResult = $this->injector === null
            ? $callback($value, $context)
            : $this->injector->invoke($callback, ['value' => $value, 'context' => $context]);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();

        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->formatMessage($message));
            }
        }
        return $result;
    }

    public function withInjector(Injector $injector): self
    {
        $new = clone $this;
        $new->injector = $injector;
        return $new;
    }
}
