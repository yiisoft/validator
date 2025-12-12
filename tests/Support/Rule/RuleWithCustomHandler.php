<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\RuleInterface;

final class RuleWithCustomHandler implements RuleInterface
{
    public function __construct(
        private readonly string $handlerClassName,
    ) {}

    public function getHandler(): string
    {
        return $this->handlerClassName;
    }
}
