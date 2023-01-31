<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\EmptyCondition\WhenNull;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;

/**
 * An optional interface for rules to implement. It allows skipping validation for a rule when the validated value is
 * "empty".
 *
 * The package ships with {@see SkipOnEmptyTrait} which already implements that interface. All you have to do is include
 * it in the rule class along with the interface.
 */
interface SkipOnEmptyInterface
{
    /**
     * Changes current "skip on empty" value. Must be immutable.
     *
     * @param bool|callable|null $value A new value.
     *
     * @return $this The new instance of a rule with a changed value.
     */
    public function skipOnEmpty(bool|callable|null $value): static;

    /**
     * Returns current "skip on empty" value.
     *
     * During pre-validation phase it will be normalized to an "empty condition" - a callable identifying when and which
     * values exactly must be considered as empty for corresponding rules to be skipped or not skipped at all.
     *
     * @return bool|callable|null A raw non-normalized value:
     *
     * - `null` - a {@see Validator::$defaultSkipOnEmptyCondition} is used. If it's `null` there too, it's equivalent to
     * `false` (see below).
     * `false` - never skip a rule (the validated value is always considered as not empty). Matching condition -
     * {@see NeverEmpty}.
     * - `true` - skip a rule when the validated value is empty: either not passed at all, `null`, an empty string (not
     * trimmed by default) or an empty array. Matching condition - {@see WhenEmpty}.
     * - `callable` - skip a rule when evaluated to `true`.
     *
     * Examples of custom callables with built-in condition:
     *
     * - `new WhenNull()` - less used built-in condition ({@see WhenNull}). Skip a rule only when the validated value is
     * `null`.
     * - `new WhenEmpty(trimString: true)` - built-in condition with custom arguments ({@see WhenEmpty}).
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
