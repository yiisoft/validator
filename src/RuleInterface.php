<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface RuleInterface
{
    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default, it returns base name of the class, first letter in lowercase.
     *
     * @return string
     */
    public function getName(): string;

    public function getHandlerClassName(): string;
}
