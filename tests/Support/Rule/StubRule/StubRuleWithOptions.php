<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\StubRule;

use Yiisoft\Validator\RuleWithOptionsInterface;

final class StubRuleWithOptions implements RuleWithOptionsInterface
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
