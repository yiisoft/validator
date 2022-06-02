<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

final class CallbackHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
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
            $result->addError($this->formatMessage($error->getMessage()), $error->getValuePath());
        }

        return $result;
    }
}
