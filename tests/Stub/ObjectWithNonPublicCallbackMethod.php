<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class ObjectWithNonPublicCallbackMethod
{
    #[Callback(method: 'validateName')]
    private string $name;

    protected static function validateName(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();
        if ($value !== 'foo') {
            $result->addError('Value must be "foo"!');
        }

        return $result;
    }
}
