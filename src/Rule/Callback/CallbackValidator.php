<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Callback;

use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

final class CallbackValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $callback = $rule->callback;
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $result->merge($error);
        }

        return $result;
    }
}
