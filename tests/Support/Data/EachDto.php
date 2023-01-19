<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;

#[Each([
    new Callback(method: 'validate'),
])]
final class EachDto
{
    public function __construct(
        public int $a,
        public int $b,
        public int $c,
    ) {
    }

    private function validate(int $value): Result
    {
        $result = new Result();

        if ($value !== 0) {
            $result->addError('Value must be zero.');
        }

        return $result;
    }
}
