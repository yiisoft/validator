<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

/**
 * An implementation for {@see SkipOnErrorInterface} intended to be included in rules. Requires an additional private
 * class property `$skipOnError`. In package rules it's `false` by default:
 *
 * ```php
 * public function __construct(
 *     // ...
 *     private bool $skipOnError = false,
 *     // ...
 * ) {
 * }
 * ```
 */
trait SkipOnErrorTrait
{
    /**
     * An immutable setter to change `$skipOnError` property.
     *
     * @param bool $value A new value. `true` means to skip the current rule when the previous one errored and `false` -
     * do not skip.
     *
     * @return $this The new instance with a changed value.
     */
    public function skipOnError(bool $value): static
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    /**
     * A getter for `$skipOnError` property.
     *
     * @return bool Current value. `true` means to skip the current rule when the previous one errored  and `false` - do
     * not skip.
     */
    public function shouldSkipOnError(): bool
    {
        return $this->skipOnError;
    }
}
