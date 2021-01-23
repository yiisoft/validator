<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
interface FormatableRuleInterface extends RuleInterface
{
    public function formatter(FormatterInterface $translator): self;
}
