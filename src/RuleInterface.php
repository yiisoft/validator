<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * A main interface for rules to implement.
 */
interface RuleInterface
{
    /**
     * Gets the name of a rule used during conversion to array.
     */
    public function getName(): string;

    public function getHandlerClassName(): string;
}
