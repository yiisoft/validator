<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Composite;

#[Composite([
    new Callback(method: 'validateA')
])]
final class CompositeWithCallbackAttribute
{
    #[Composite([
        new Callback(method: 'validateB')
    ])]
    private int $b = 7;

    private function validateA(): Result
    {
        return (new Result())->addError('Invalid A.');
    }

    private function validateB(): Result
    {
        return (new Result())->addError('Invalid B.');
    }
}
