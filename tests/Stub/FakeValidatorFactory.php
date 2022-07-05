<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\GroupRuleHandler;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

final class FakeValidatorFactory
{
    public static function make(): Validator
    {
        $ruleHandlerResolver = new SimpleRuleHandlerContainer();
        $validator = new Validator($ruleHandlerResolver);

        $ruleHandlerResolver->addInstance(new CompositeHandler($validator));
        $ruleHandlerResolver->addInstance(new EachHandler($validator, null));
        $ruleHandlerResolver->addInstance(new GroupRuleHandler($validator, null));
        $ruleHandlerResolver->addInstance(new NestedHandler($validator, null));

        return $validator;
    }
}
