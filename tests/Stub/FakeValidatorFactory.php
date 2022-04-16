<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\Rule\AtLeast\AtLeastHandler;
use Yiisoft\Validator\Rule\Boolean\BooleanHandler;
use Yiisoft\Validator\Rule\Callback\CallbackHandler;
use Yiisoft\Validator\Rule\CompareTo\CompareToHandler;
use Yiisoft\Validator\Rule\Count\CountHandler;
use Yiisoft\Validator\Rule\Each\EachHandler;
use Yiisoft\Validator\Rule\Email\EmailHandler;
use Yiisoft\Validator\Rule\GroupRule\GroupRuleHandler;
use Yiisoft\Validator\Rule\HasLength\HasLengthHandler;
use Yiisoft\Validator\Rule\InRange\InRangeHandler;
use Yiisoft\Validator\Rule\Ip\IpHandler;
use Yiisoft\Validator\Rule\Json\JsonHandler;
use Yiisoft\Validator\Rule\Nested\NestedHandler;
use Yiisoft\Validator\Rule\Number\NumberHandler;
use Yiisoft\Validator\Rule\Regex\RegexHandler;
use Yiisoft\Validator\Rule\Required\RequiredHandler;
use Yiisoft\Validator\Rule\Subset\SubsetHandler;
use Yiisoft\Validator\Rule\Url\UrlHandler;
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
