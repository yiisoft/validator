<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * An optional interface for rules to implement. It allows applying validation for a rule within a group of other rules
 * under certain conditions.
 *
 * The package ships with {@see WhenTrait} which already implements that interface. All you have to do is include it in
 * the rule class along with the interface.
 *
 * @psalm-type WhenType = null|Closure(mixed, ValidationContext):bool
 */
interface WhenInterface
{
    /**
     * Changes current "when" value. Must be immutable.
     *
     * @psalm-param WhenType $value A new value:
     *
     * - `null` - always apply the validation.
     * - `callable` - apply the validation depending on a return value: `true` - apply, `false` - do not apply.
     *
     * A callable must have the following signature:
     *
     * ```php
     * function (mixed $value, ValidationContext $context): bool;
     * ```
     *
     * An example of applying validation depending on other property value:
     *
     * ```php
     * static function (mixed $value, ValidationContext $context): bool {
     *     return $context->getDataSet()->getAttributeValue('country') === Country::USA;
     * }
     * ```
     *
     * @return $this The new instance of a rule with a changed value.
     *
     * @see ValidationContext for publicly available methods and properties for building condition.
     */
    public function when(Closure|null $value): static;

    /**
     * Returns current "when" value.
     *
     * @psalm-return WhenType Current value:
     *
     * - `null` - always apply the validation.
     * - `callable` - apply the validation depending on a return value: `true` - apply, `false` - do not apply.
     */
    public function getWhen(): Closure|null;
}
