<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * A helper class used to propagate options' values from a single parent rule to its single child rule.
 */
final class PropagateOptionsHelper
{
    /**
     * Propagates options' values from a single parent rule to its single child rule. The following options' values are
     * propagated:
     *
     * - `$skipOnEmpty` (both rules must implement {@see SkipOnEmptyInterface}).
     * - `$skipOnError` (both rules must implement {@see SkipOnErrorInterface}).
     * - `$when` (both rules must implement {@see WhenInterface}).
     *
     * @param RuleInterface $parentRule A parent rule which options' values need to be propagated.
     * @param iterable<RuleInterface> $childRules A child rule which options' values must be changed to be the same as in parent.
     *
     * @return RuleInterface[] A child rule with changed options' values or unchanged if none of the required interfaces
     * were implemented.
     */
    public static function propagate(RuleInterface $parentRule, iterable $childRules): array
    {
        $rules = [];
        foreach ($childRules as $childRule) {
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

            $rules[] = $childRule;
        }

        return $rules;
    }
}
