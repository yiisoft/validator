<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Validator;

class FakeValidatorFactory
{
    public static function make()
    {
        return new Validator(FakeRuleStorageFactory::make());
    }
}
