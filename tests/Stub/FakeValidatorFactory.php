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
            AtLeastHandler::class => new AtLeastHandler(),
            BooleanHandler::class => new BooleanHandler(),
            CallbackHandler::class => new CallbackHandler(),
            CompareToHandler::class => new CompareToHandler(),
            CountHandler::class => new CountHandler(),
            EachHandler::class => new EachHandler(),
            EmailHandler::class => new EmailHandler(),
            GroupRuleHandler::class => new GroupRuleHandler(),
            HasLengthHandler::class => new HasLengthHandler(),
            InRangeHandler::class => new InRangeHandler(),
            IpHandler::class => new IpHandler(),
            JsonHandler::class => new JsonHandler(),
            NumberHandler::class => new NumberHandler(),
            RegexHandler::class => new RegexHandler(),
            RequiredHandler::class => new RequiredHandler(),
            SubsetHandler::class => new SubsetHandler(),
            UrlHandler::class => new UrlHandler(),
            NestedHandler::class => new NestedHandler(),
        ]));
    }
}
