<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;

final class DatasetWithPostValidationHook implements PostValidationHookInterface
{
    public static $hookCalled = false;

    public function getAttributeValue(string $attribute): mixed
    {
        return null;
    }

    public function getData(): ?array
    {
        return null;
    }

    public function hasAttribute(string $attribute): bool
    {
        return false;
    }

    public function processValidationResult(Result $result): void
    {
        self::$hookCalled = true;
    }
}
