<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Callback;

use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;

final class CallbackValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return Callback::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $callback = $config->callback;
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $result->addError(($error->getMessage()), $error->getValuePath());
        }

        return $result;
    }
}
