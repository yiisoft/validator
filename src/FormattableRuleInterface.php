<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * The interface marks a rule that gets error messages by formatting based on a pattern and a set of parameters.
 * The interface allows to set formatter to be used.
 */
interface FormattableRuleInterface extends RuleInterface
{
    public function formatter(FormatterInterface $translator): self;
}
