<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

final class FakeValidatorFactory
{
    public static function make(): Validator
    {
        $translator = (new TranslatorFactory())->create();
        return new Validator(new SimpleRuleHandlerContainer(), $translator);
    }
}
