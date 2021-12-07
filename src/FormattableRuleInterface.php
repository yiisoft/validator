<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * The interface marks a rule that gets error messages by formatting based on a pattern and a set of parameters.
 * The interface allows setting formatter to be used.
 */
interface FormattableRuleInterface extends RuleInterface
{
    /**
     * @param FormatterInterface|null $formatter Formatter to use.
     */
    public function withFormatter(?FormatterInterface $formatter): self;
}
