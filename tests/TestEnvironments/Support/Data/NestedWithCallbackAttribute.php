<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Support\Data;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Nested;

#[Nested([
    'a' => new Callback(method: 'validateA'),
])]
final class NestedWithCallbackAttribute
{
    private int $a = 7;

    #[Nested([
        'x' => new Callback(method: 'validateB'),
    ])]
    private array $b = [
        'x' => 5,
    ];

    private function validateA(): Result
    {
        return (new Result())->addError('Invalid A.');
    }

    private function validateB(): Result
    {
        return (new Result())->addError('Invalid B.');
    }
}
