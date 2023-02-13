<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

/**
 * Enum for {@see AbstractCompare::$type}.
 */
final class CompareType
{
    /**
     * Constant for specifying the comparison as original values. Values will not be converted to other type before
     * comparison.
     *
     * @see AbstractCompare::$type
     */
    public const ORIGINAL = 'original';
    /**
     * Constant for specifying the comparison as string values. Values will be converted to strings before comparison.
     *
     * @see AbstractCompare::$type
     */
    public const STRING = 'string';
    /**
     * Constant for specifying the comparison as numeric values. Values will be converted to float numbers before
     * comparison.
     *
     * @see AbstractCompare::$type
     */
    public const NUMBER = 'number';
}
