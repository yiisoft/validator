<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\StubRule;

use Yiisoft\Validator\DumpedRuleInterface;

final class StubDumpedRule implements DumpedRuleInterface
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

    public function getHandler(): string
    {
        return StubRuleHandler::class;
    }
}
