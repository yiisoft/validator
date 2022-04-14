<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Json;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Json\Json;
use Yiisoft\Validator\Rule\Json\JsonValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class JsonValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Json();
        $message = $rule->message;
        $errors = [new Error($message, [])];

        return [
            [$rule, '{"name": "tester"', $errors],
            [$rule, '{"name": tester}', $errors],

            [$rule, ['json'], $errors],
            [$rule, 10, $errors],
            [$rule, null, $errors],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Json();

        return [
            // JSON test from http://www.json.org/JSON_checker/test/pass1.json
            [
                $rule,
                <<<'JSON'
[
    "JSON Test Pattern pass1",
    {"object with 1 member":["array with 1 element"]},
    {},
    [],
    -42,
    true,
    false,
    null,
    {
        "integer": 1234567890,
        "real": -9876.543210,
        "e": 0.123456789e-12,
        "E": 1.234567890E+34,
        "":  23456789012E66,
        "zero": 0,
        "one": 1,
        "space": " ",
        "quote": "\"",
        "backslash": "\\",
        "controls": "\b\f\n\r\t",
        "slash": "/ & \/",
        "alpha": "abcdefghijklmnopqrstuvwyz",
        "ALPHA": "ABCDEFGHIJKLMNOPQRSTUVWYZ",
        "digit": "0123456789",
        "0123456789": "digit",
        "special": "`1~!@#$%^&*()_+-={':[,]}|;.</>?",
        "hex": "\u0123\u4567\u89AB\uCDEF\uabcd\uef4A",
        "true": true,
        "false": false,
        "null": null,
        "array":[  ],
        "object":{  },
        "address": "50 St. James Street",
        "url": "http://www.JSON.org/",
        "comment": "// /* <!-- --",
        "# -- --> */": " ",
        " s p a c e d " :[1,2 , 3

,

4 , 5        ,          6           ,7        ],"compact":[1,2,3,4,5,6,7],
        "jsontext": "{\"object with 1 member\":[\"array with 1 element\"]}",
        "quotes": "&#34; \u0022 %22 0x22 034 &#x22;",
        "\/\\\"\uCAFE\uBABE\uAB98\uFCDE\ubcda\uef4A\b\f\n\r\t`1~!@#$%^&*()_+-=[]{}|;:',./<>?"
: "A key can be any string"
    },
    0.5 ,98.6
,
99.44
,

1066,
1e1,
0.1e1,
1e-1,
1e00,2e+00,2e-00
,"rosebud"]
JSON,
            ],
            // JSON test from http://www.json.org/JSON_checker/test/pass2.json
            [$rule, '[[[[[[[[[[[[[[[[[[["Not too deep"]]]]]]]]]]]]]]]]]]]'],
            // JSON test from http://www.json.org/JSON_checker/test/pass3.json
            [
                $rule,
                <<<'JSON'
{
    "JSON Test Pattern pass3": {
        "The outermost value": "must be an object or array.",
        "In this test": "It is an object."
    }
}
JSON,
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Json(message: 'bad json'),
                '',
                [new Error('bad json', [])],
            ],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new JsonValidator();
    }
}
