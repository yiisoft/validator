<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use InvalidArgumentException;

/**
 * An exception used by rule handlers to guarantee that passed rule have desired type. Every handler's validation code
 * must start with this check. An example for `MyRule` and `MyRuleHandler`:
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
 *             throw new UnexpectedRuleException(AtLeast::class, $rule);
 *         }
 *
 *         // ...
 *         $result = new Result();
 *         // ...
 *
 *         return $result;
 *     }
 * }
 */
final class UnexpectedRuleException extends InvalidArgumentException
{
    public function __construct(
        /**
         * @var string Expected class name of a rule.
         */
        string $expectedClassName,
        /**
         * @var object An actual given object that's not an instance of `$expectedClassName`.
         */
        object $actualObject
    )
    {
        $actualClassName = $actualObject::class;
        $message = "Expected \"$expectedClassName\", but {$actualClassName} given.";

        parent::__construct($message);
    }
}
