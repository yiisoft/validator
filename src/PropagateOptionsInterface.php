<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface implemented by rules that can propagate their common options (such as `skipOnEmpty`, `skipOnError`,
 * `when`) to child rules as an alternative way of specifying them explicitly in every child rule.
 */
interface PropagateOptionsInterface
{
    public function propagateOptions(): void;
}
