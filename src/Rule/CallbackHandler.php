<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class CallbackHandler implements RuleHandlerInterface
{
    /**
     * @throws InvalidCallbackReturnTypeException
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Callback) {
            throw new UnexpectedRuleException(Callback::class, $rule);
        }

        $callback = $rule->getCallback();
        if ($callback === null) {
            throw new InvalidArgumentException('Using method outside of attribute scope is prohibited.');
        }

        $callbackResult = $callback($value, $rule, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $result->addError(
                $error->getMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
                $error->getValuePath(),
            );
        }

        return $result;
    }
}
