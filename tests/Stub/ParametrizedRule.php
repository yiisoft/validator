<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

final class ParametrizedRule implements ParametrizedRuleInterface
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

    public function validate($value, ValidationContext $context = null): Result
    {
        return new Result();
    }
}
