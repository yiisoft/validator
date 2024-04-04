<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use InvalidArgumentException;
use Throwable;

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
 */
final class UnexpectedRuleException extends InvalidArgumentException
{
    /**
     * @param string|string[] $expectedClassName Expected class name(s) of a rule.
     * @param object $actualObject An actual given object that's not an instance of `$expectedClassName`.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(
        string|array $expectedClassName,
        object $actualObject,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf(
                'Expected "%s", but "%s" given.',
                implode('", "', (array) $expectedClassName),
                $actualObject::class
            ),
            $code,
            $previous,
        );
    }
}
