<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;

final class DataSetWithPostValidationHook implements DataSetInterface, PostValidationHookInterface
{
    public bool $hookCalled = false;

    public function getPropertyValue(string $property): mixed
    {
        return null;
    }

    public function getData(): ?array
    {
        return null;
    }

    public function hasProperty(string $property): bool
    {
        return false;
    }

    public function processValidationResult(Result $result): void
    {
        $this->hookCalled = true;
    }
}
