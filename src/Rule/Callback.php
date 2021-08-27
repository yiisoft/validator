<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

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

    public static function rule(callable $callback): self
    {
        $rule = new self();
        $rule->callback = $callback;
        return $rule;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $callback = $this->callback;
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();

        if ($callbackResult->isNotValid()) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->formatMessage($message));
            }
        }
        return $result;
    }
}
