<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An optional interface for rules to implement. Allows to propagate their common options' values to child rules as an
 * alternative way of specifying them explicitly in every child rule.
 */
interface PropagateOptionsInterface
{
    /**
     * A method for implementing propagation options' values from parent to child rules.
     */
    public function propagateOptions(): void;
}
