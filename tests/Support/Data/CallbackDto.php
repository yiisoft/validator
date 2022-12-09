<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

#[Callback('\Yiisoft\Validator\Tests\Support\Data\validate')]
final class CallbackDto
{
    public function __construct(
        public int $a,
        public int $b,
    ) {
    }
}

function validate(CallbackDto $dto): Result
{
    return (new Result())->addError($dto->a . ' / ' . $dto->b);
}
