<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface RuleInterface
{
    /**
     * Get name of a rule used when rule is converted to array.
     */
    public function getName(): string;

    public function getHandlerClassName(): string;
}
