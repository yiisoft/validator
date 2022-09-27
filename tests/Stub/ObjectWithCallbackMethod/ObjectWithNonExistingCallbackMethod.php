<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub\ObjectWithCallbackMethod;

use Yiisoft\Validator\Rule\Callback;

final class ObjectWithNonExistingCallbackMethod
{
    #[Callback(method: 'validateName')]
    private string $name;
}
