<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\Callback;

final class ObjectWithNonExistingCallbackMethod
{
    #[Callback(method: 'validateName')]
    private string $name;
}
