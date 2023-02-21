<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Support\Data;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\StopOnError;

#[StopOnError([
    new Callback(method: 'validateA'),
    new Callback(method: 'validateB'),
])]
final class StopOnErrorDto
{
    private function validateA(): Result
    {
        return (new Result())->addError('error A');
    }

    private function validateB(): Result
    {
        return (new Result())->addError('error B');
    }
}
