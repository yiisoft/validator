<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates a value using a callback.
 *
 * @see Callback
 */
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

        $result = $callback($rule->getObjectValidated() ?? $value, $rule, $context);
        if (!$result instanceof Result) {
            throw new InvalidCallbackReturnTypeException($result);
        }

        return $result;
    }
}
