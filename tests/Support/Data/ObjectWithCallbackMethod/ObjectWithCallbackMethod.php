<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class ObjectWithCallbackMethod
{
    #[Callback(method: 'validateName')]
    private string $name;

    public static function validateName(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();
        if ($value !== 'foo') {
            $result->addError('The value must be "foo"!');
        }

        return $result;
    }
}
