<?php

use Yiisoft\Validator\Rule;

return [
    'yiisoft/validator' => [
        'ruleHandlers' => [
            Rule\AtLeastHandler::class,
            Rule\BooleanHandler::class,
            Rule\CallbackHandler::class,
            Rule\CompareToHandler::class,
            Rule\CountHandler::class,
            Rule\EachHandler::class,
            Rule\EmailHandler::class,
            Rule\GroupRuleHandler::class,
            Rule\HasLengthHandler::class,
            Rule\InRangeHandler::class,
            Rule\IpHandler::class,
            Rule\JsonHandler::class,
            Rule\NestedHandler::class,
            Rule\NumberHandler::class,
            Rule\RegexHandler::class,
            Rule\RequiredHandler::class,
            Rule\SubsetHandler::class,
            Rule\UrlHandler::class,
        ],
    ],
];