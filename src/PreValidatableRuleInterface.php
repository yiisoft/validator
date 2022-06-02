<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

interface PreValidatableRuleInterface
{
    public function isSkipOnEmpty(): bool;

    public function isSkipOnError(): bool;

    public function getWhen(): ?Closure;
}
