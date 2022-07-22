<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\SerializableRuleInterface;

final class Rule implements SerializableRuleInterface
{
    private array $options;
    private string $name;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
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
