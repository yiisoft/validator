<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\SimpleRuleHandlerResolver;
use Yiisoft\Validator\Validator;

final class FakeValidatorFactory
{
    public static function make(): Validator
    {
        return new Validator(new SimpleRuleHandlerResolver());
    }
}
