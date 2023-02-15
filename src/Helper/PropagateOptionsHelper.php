<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * A helper class used to propagate options' values from a single parent rule to its child rules at all nesting levels
 * recursively.
 */
final class PropagateOptionsHelper
{
    /**
     * Propagates options' values from a single parent rule to its all child rules at all nesting levels recursively.
     * The following options' values are propagated:
     *
     * - `$skipOnEmpty` (both rules must implement {@see SkipOnEmptyInterface}).
     * - `$skipOnError` (both rules must implement {@see SkipOnErrorInterface}).
     * - `$when` (both rules must implement {@see WhenInterface}).
     *
     * @param RuleInterface $parentRule A parent rule which options' values need to be propagated.
     * @param iterable<RuleInterface> $childRules Direct child rules for this particular parent rule which options'
     * values must be changed to be the same as in parent rule.
     *
     * @return list<RuleInterface> A list of child rules of the same nesting level with changed options' values or
     * unchanged if none of the required interfaces were implemented. The order is preserved.
     */
    public static function propagate(RuleInterface $parentRule, iterable $childRules): array
    {
        $rules = [];
        foreach ($childRules as $childRule) {
            $rules[] = self::propagateForRule($parentRule, $childRule);
        }
        return $rules;
    }

    /**
     * Performs propagation of options' values for a single pair of one parent rule and one of its direct child rules.
     * If the child rule also supports such propagation, it delegates the further propagation to
     * {@see PropagateOptionsInterface::propagateOptions()} implementation in this child rule.
     *
     * @param RuleInterface $parentRule A parent rule which options' values need to be propagated.
     * @param RuleInterface $childRule One of the direct child rules for this particular parent rule which options'
     * values must be changed to be the same as in parent rule.
     *
     * @return RuleInterface The same child rule instance with changed options' values or unchanged if none of the
     * required interfaces were implemented.
     */
    public static function propagateForRule(RuleInterface $parentRule, RuleInterface $childRule): RuleInterface
    {
        if ($parentRule instanceof SkipOnEmptyInterface && $childRule instanceof SkipOnEmptyInterface) {
            $childRule = $childRule->skipOnEmpty($parentRule->getSkipOnEmpty());
        }

        if ($parentRule instanceof SkipOnErrorInterface && $childRule instanceof SkipOnErrorInterface) {
            $childRule = $childRule->skipOnError($parentRule->shouldSkipOnError());
        }

        if ($parentRule instanceof WhenInterface && $childRule instanceof WhenInterface) {
            $childRule = $childRule->when($parentRule->getWhen());
        }

        if ($childRule instanceof PropagateOptionsInterface) {
            $childRule->propagateOptions();
        }

        return $childRule;
    }
}
