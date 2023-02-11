<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;

final class ObjectWithPostValidationHook implements PostValidationHookInterface
{
    public bool $hookCalled = false;

    public function processValidationResult(Result $result): void
    {
        $this->hookCalled = true;
    }
}
