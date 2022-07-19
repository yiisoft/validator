<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;

class TestRuleHandlerResolverFactory
{
    public static function create(): RuleHandlerResolverInterface
    {
        $translator = new NullTranslator();
        $instances = [
            Rule\AtLeastHandler::class => new Rule\AtLeastHandler($translator),
            Rule\BooleanHandler::class => new Rule\BooleanHandler($translator),
            Rule\CallbackHandler::class => new Rule\CallbackHandler($translator),
            Rule\CompareHandler::class => new Rule\CompareHandler($translator),
            Rule\CompositeHandler::class => new Rule\CompositeHandler(),
            Rule\CountHandler::class => new Rule\CountHandler($translator),
            Rule\EachHandler::class => new Rule\EachHandler($translator),
            Rule\EmailHandler::class => new Rule\EmailHandler($translator),
            Rule\GroupRuleHandler::class => new Rule\GroupRuleHandler($translator),
            Rule\HasLengthHandler::class => new Rule\HasLengthHandler($translator),
            Rule\InRangeHandler::class => new Rule\InRangeHandler($translator),
            Rule\IpHandler::class => new Rule\IpHandler($translator),
            Rule\JsonHandler::class => new Rule\JsonHandler($translator),
            Rule\NestedHandler::class => new Rule\NestedHandler($translator),
            Rule\NumberHandler::class => new Rule\NumberHandler($translator),
            Rule\RegexHandler::class => new Rule\RegexHandler($translator),
            Rule\RequiredHandler::class => new Rule\RequiredHandler($translator),
            Rule\SubsetHandler::class => new Rule\SubsetHandler($translator),
            Rule\UrlHandler::class => new Rule\UrlHandler($translator),
        ];
        return new SimpleRuleHandlerContainer($instances);
    }
}
