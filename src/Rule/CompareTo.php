<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;

/**
 * Defines validation options to check that the specified value match with another value or attribute.
 *
 * The value being compared with a constant {@see CompareTo::$compareValue}, which is set
 * in the constructor.
 *
 * It supports different comparison operators, specified via the {@see CompareTo::$operator}.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to change {@see CompareTo::$type} to
 * {@see CompareTo::TYPE_NUMBER} to enable numeric comparison.
 *
 * @see CompareHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class CompareTo extends Compare
{
    public function getName(): string
    {
        return 'compareTo';
    }
}
