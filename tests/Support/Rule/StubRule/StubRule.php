<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\StubRule;

use Yiisoft\Validator\SerializableRuleInterface;

final class StubRule implements SerializableRuleInterface
{
    public function __construct(private string $name, private array $options)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getHandlerClassName(): string
    {
        return StubRuleHandler::class;
    }
}
