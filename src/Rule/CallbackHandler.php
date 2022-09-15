<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class CallbackHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Callback) {
            throw new UnexpectedRuleException(Callback::class, $rule);
        }

        $callback = $rule->getCallback();
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $result->addError(
                message: $error->getMessage(),
                valuePath: $error->getValuePath(),
                parameters: ['value' => $value]
            );
        }

        return $result;
    }
}
