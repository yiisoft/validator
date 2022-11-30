<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\EmptyCriteria\NeverEmpty;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\EmptyCriteria\WhenNull;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;

/**
 * An optional interface for rules to implement. It allows skipping validation for a rule when the validated value is
 * "empty". Requires an additional private class property `$skipOnEmpty`. In package rules it's `null` by default:
 *
 * ```php
 * public function __construct(
 *     // ...
 *     private mixed $skipOnEmpty = null,
 *     // ...
 * ) {
 * }
 * ```
 *
 * The package ships with {@see SkipOnEmptyTrait} which already implements that interface. All you have to do is include
 * it in the rule class.
 */
interface SkipOnEmptyInterface
{
    /**
     * A setter to change `$skipOnEmpty` property. Must be implemented following immutability concept:
     *
     * ```php
     * public function skipOnEmpty(bool|callable|null $value): static;
     * {
     *     $new = clone $this;
     *     $new->skipOnEmpty = $value;
     *
     *     return $new;
     * }
     * ```
     *
     * @param bool|callable|null $value
     *
     * @return $this
     */
    public function skipOnEmpty(bool|callable|null $value): static;

    /**
     * A getter for `$skipOnEmpty` property. Returned value is unchanged. Typical implementation is just a simple
     * returning of the property:
     *
     * ```php
     * public function getSkipOnEmpty(): bool|callable|null
     * {
     *     return $this->skipOnEmpty;
     * }
     * ```
     *
     * During pre-validation phase it will be normalized to an "empty criteria" - a callable identifying when and which
     * values exactly must be considered as empty and skipped or not skipped at all.
     *
     * @return bool|callable|null A raw value set in the constructor:
     *
     * - `null` and `false` - never skip a rule (the validated value is always considered as not empty). Matching
     * criteria - {@see NeverEmpty}.
     * - `true` - skip a rule when the validated value is empty: either not passed at all, `null`, an empty string (not
     * trimmed by default) or an empty array. Matching criteria - {@see WhenEmpty}.
     * - `callable` - skip a rule when evaluated to `true`.
     *
     * Examples of custom callables with built-in criteria:
     *
     * - `new WhenNull()` - less used built-in criteria ({@see WhenNull}). Skip a rule only when the validated value is
     * `null`.
     * - `new WhenEmpty(trimString: true)` - built-in criteria with custom arguments ({@see WhenEmpty}).
     *
     * A custom callable for skipping only when a value is zero:
     *
     * ```php
     * static function (mixed $value, bool $isAttributeMissing): bool {
     * {
     *     return $value === 0;
     * }
     *```
     *
     * An equivalent class implementing `__invoke()` magic method:
     *
     * ```php
     * final class SkipOnZero
     * {
     *     public function __invoke(mixed $value, bool $isAttributeMissing): bool
     *     {
     *         return $value === 0;
     *     }
     * }
     * ```
     */
    public function getSkipOnEmpty(): bool|callable|null;
}
