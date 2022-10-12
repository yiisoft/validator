<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\RuleInterface;

final class RuleWithCustomHandler implements RuleInterface
{
    public function __construct(
        private string $handlerClassName,
    ) {
    }

    public function getName(): string
    {
        return 'rule-with-custom-handler';
    }

    public function getHandlerClassName(): string
    {
        return $this->handlerClassName;
    }
}
