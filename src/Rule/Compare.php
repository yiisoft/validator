<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;

/**
 * Defines validation options to compare the specified value with "target" value provided directly
 * ({@see GreaterThanOrEqual::$targetValue}) or within an attribute ({@see GreaterThanOrEqual::$targetProperty}).
 *
 * The default comparison is based on number values (including float values). It's also possible to compare values as
 * strings byte by byte and compare original values as is. See {@see GreaterThanOrEqual::$type} for all possible
 * options.
 *
 * It supports different comparison operators, specified via the {@see Compare::$operator}.
 *
 * There are shortcut classes to use instead of specifying operator manually:
 *
 * - {@see Equal} is a shortcut for `new Compare(operator: '==')` and `new Compare(operator: '===')`.
 * - {@see NotEqual} is a shortcut for `new Compare(operator: '!=')` and `new Compare(operator: '!==')`.
 * - {@see GreaterThan} is a shortcut for `new Compare(operator: '>')`.
 * - {@see GreaterThanOrEqual} is a shortcut for `new Compare(operator: '>=')`.
 * - {@see LessThan} is a shortcut for `new Compare(operator: '<')`.
 * - {@see LessThanOrEqual} is a shortcut for `new Compare(operator: '<=')`.
 *
 * @see CompareHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Compare extends AbstractCompare
{
}
