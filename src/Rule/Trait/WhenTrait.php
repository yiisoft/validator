<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\WhenInterface;

/**
 * An implementation for {@see WhenInterface} intended to be included in rules. Requires an additional private class
 * property `$when`. In package rules it's `null` by default:
 *
 * ```php
 * public function __construct(
 *     // ...
 *     private Closure|null $when = null
 *     // ...
 * ) {
 * }
 * ```
 *
 * @psalm-import-type WhenType from WhenInterface
 */
trait WhenTrait
{
    /**
     * An immutable setter to change `$when` property.
     *
     * @psalm-param WhenType $value A new value:
     *
     * - `null` - always apply the validation.
     * - `callable` - apply the validation depending on a return value: `true` - apply, `false` - do not apply.
     *
     * @return $this The new instance with a changed value.
     */
    public function when(Closure|null $value): static
    {
        $new = clone $this;
        $new->when = $value;
        return $new;
    }

    /**
     * A getter for `$when` property.
     *
     * @psalm-return WhenType Current value:
     *
     * - `null` - always apply the validation.
     * - `callable` - apply the validation depending on a return value: `true` - apply, `false` - do not apply.
     */
    public function getWhen(): Closure|null
    {
        return $this->when;
    }
}
