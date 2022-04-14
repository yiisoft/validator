<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\ValidatorClassNameTrait;
use Yiisoft\Validator\RuleInterface;

final class Rule implements RuleInterface
{
    use ValidatorClassNameTrait;

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
}
