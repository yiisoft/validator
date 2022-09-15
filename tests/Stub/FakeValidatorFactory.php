<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DatasetNormalizerValidatorDecorator;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\TranslateValidatorDecorator;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

final class FakeValidatorFactory
{
    public static function make(): ValidatorInterface
    {
        $translator = (new TranslatorFactory())->create();
        return new TranslateValidatorDecorator(
            new DatasetNormalizerValidatorDecorator(
                new Validator(new SimpleRuleHandlerContainer($translator)),
            ),
            $translator,
        );
    }
}
