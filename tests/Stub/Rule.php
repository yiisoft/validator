<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\SerializableRuleInterface;

final class Rule implements SerializableRuleInterface
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
        return RuleHandler::class;
    }
}
