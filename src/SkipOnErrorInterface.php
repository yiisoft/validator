<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;

/**
 * An optional interface for rules to implement. It allows skipping validation for a rule within a group of other rules
 * if a previous rule (not the current one!) did not pass the validation.
 *
 * The package ships with {@see SkipOnErrorTrait} which already implements that interface. All you have to do is include
 * it in the rule class along with the interface.
 */
interface SkipOnErrorInterface
{
    /**
     * Changes current "skip on error" value. Must be done following immutability concept.
     *
     * @param bool $value A new value. `true` means to skip the current rule when the previous one errored and `false` -
     * do not skip.
     *
     * @return $this The new instance of a rule with a changed value.
     */
    public function skipOnError(bool $value): static;

    /**
     * Returns current "skip on error" value.
     *
     * @return bool Current value. `true` means to skip the current rule when the previous one errored  and `false` - do
     * not skip.
     */
    public function shouldSkipOnError(): bool;
}
