<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * An interface for rule handlers to implement. While a rule contains a set of constraint configuration options, a
 * handler performs an actual validation using them against a provided value. Every rule has only one matching rule
 * handler.
 */
interface RuleHandlerInterface
{
    /**
     * Validates a value according to a matching rule's configuration options.
     *
     * Every handler's validation code must check if a rule matches the handler first ({@see UnexpectedRuleException}).
     * An example for `MyRule` and `MyRuleHandler`:
     *
     * ```php
     * use Yiisoft\Validator\Exception\UnexpectedRuleException;
     * use Yiisoft\Validator\Result;
     * use Yiisoft\Validator\RuleHandlerInterface;
     * use Yiisoft\Validator\ValidationContext;
     *
     * final class MyRuleHandler implements RuleHandlerInterface
     * {
     *     public function validate(mixed $value, object $rule, ValidationContext $context): Result
     *     {
     *         if (!$rule instanceof MyRule) {
     *             throw new UnexpectedRuleException(MyRule::class, $rule);
     *         }
     *
     *         // ...
     *         $result = new Result();
     *         // ...
     *
     *         return $result;
     *     }
     * }
     * ```
     *
     * @param mixed $value A validated value of any type.
     * @param object $rule A rule instance containing configuration parameters.
     * @param ValidationContext $context A validation context instance.
     *
     * @return Result A validation result instance.
     *
     * @internal Should never be called directly. Use {@see ValidatorInterface} instead.
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result;
}
