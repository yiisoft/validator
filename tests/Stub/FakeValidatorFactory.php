<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Rule\BooleanHandler;
use Yiisoft\Validator\Rule\CallbackHandler;
use Yiisoft\Validator\Rule\CompareToHandler;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\EmailHandler;
use Yiisoft\Validator\Rule\GroupRuleHandler;
use Yiisoft\Validator\Rule\HasLengthHandler;
use Yiisoft\Validator\Rule\InRangeHandler;
use Yiisoft\Validator\Rule\IpHandler;
use Yiisoft\Validator\Rule\JsonHandler;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\NumberHandler;
use Yiisoft\Validator\Rule\RegexHandler;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\Rule\SubsetHandler;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\StaticRuleHandlerResolver;
use Yiisoft\Validator\Validator;

final class FakeValidatorFactory
{
    public static function make(): Validator
    {
        return new Validator(new StaticRuleHandlerResolver([
            AtLeastHandler::class,
            BooleanHandler::class,
            CallbackHandler::class,
            CompareToHandler::class,
            CountHandler::class,
            EachHandler::class,
            EmailHandler::class,
            GroupRuleHandler::class,
            HasLengthHandler::class,
            InRangeHandler::class,
            IpHandler::class,
            JsonHandler::class,
            NumberHandler::class,
            RegexHandler::class,
            RequiredHandler::class,
            SubsetHandler::class,
            UrlHandler::class,
            NestedHandler::class,
        ]));
    }
}
