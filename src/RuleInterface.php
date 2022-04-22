<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface RuleInterface
{
    public function getHandlerClassName(): string;
}
