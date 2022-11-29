<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Json;
use Yiisoft\Validator\Rule\JsonHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class JsonTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Json();
        $this->assertSame('json', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Json(),
                [
                    'incorrectInputMessage' => [
                        'template' => 'The value must have a string type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The value is not JSON.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            // JSON test from http://www.json.org/JSON_checker/test/pass1.json
            [
                <<<'JSON_WRAP'
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
JSON_WRAP
                ,
                [new Json()],
            ],
            // JSON test from http://www.json.org/JSON_checker/test/pass2.json
            ['[[[[[[[[[[[[[[[[[[["Not too deep"]]]]]]]]]]]]]]]]]]]', [new Json()]],
            // JSON test from http://www.json.org/JSON_checker/test/pass3.json
            [
                <<<'JSON_WRAP'
{
    "JSON Test Pattern pass3": {
        "The outermost value": "must be an object or array.",
        "In this test": "It is an object."
    }
}
JSON_WRAP
                ,
                [new Json()],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputErrors = ['' => ['The value must have a string type.']];
        $errors = ['' => ['The value is not JSON.']];

        return [
            'incorrect input, array' => [['json'], [new Json()], $incorrectInputErrors],
            'incorrect input, integer' => [10, [new Json()], $incorrectInputErrors],
            'incorrect input, null' => [null, [new Json()], $incorrectInputErrors],
            'custom incorrect input message' => [
                ['json'],
                [new Json(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                ['json'],
                [new Json(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => ['json']],
                ['data' => new Json(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - array.']],
            ],

            ['{"name": "tester"', [new Json()], $errors],
            ['{"name": tester}', [new Json()], $errors],

            'custom message' => ['bad json', [new Json(message: 'Custom message.')], ['' => ['Custom message.']]],
            'custom message with parameters' => [
                'bad json',
                [new Json(message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - bad json.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 'bad json'],
                ['data' => new Json(message: 'Attribute - {attribute}, value - {value}.')],
                ['data' => ['Attribute - data, value - bad json.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Json(), new Json(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Json(), new Json(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Json::class, JsonHandler::class];
    }
}
