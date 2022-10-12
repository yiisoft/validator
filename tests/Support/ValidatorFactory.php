<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support;

use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Tests\Support\TranslatorFactory;
use Yiisoft\Validator\Validator;

final class ValidatorFactory
{
    public static function make(): Validator
    {
        $translator = (new TranslatorFactory())->create();
        return new Validator(new SimpleRuleHandlerContainer($translator));
    }
}
