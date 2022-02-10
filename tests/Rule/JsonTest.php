<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Json;

/**
 * @group validators
 */
class JsonTest extends TestCase
{
    public function testInvalidJsonValidate(): void
    {
        $val = Json::rule();
        $this->assertFalse($val->validate('{"name": "tester"')->isValid());
        $this->assertFalse($val->validate('{"name": tester}')->isValid());
    }

    public function testInvalidTypeValidate(): void
    {
        $val = Json::rule();
        $this->assertFalse($val->validate(['json'])->isValid());
        $this->assertFalse($val->validate(10)->isValid());
        $this->assertFalse($val->validate(null)->isValid());
    }

    public function testValidValueValidate(): void
    {
        // JSON test from http://www.json.org/JSON_checker/test/pass1.json
        $json1 = <<<'JSON'
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
JSON;
        // JSON test from http://www.json.org/JSON_checker/test/pass2.json
        $json2 = '[[[[[[[[[[[[[[[[[[["Not too deep"]]]]]]]]]]]]]]]]]]]';
        // JSON test from http://www.json.org/JSON_checker/test/pass3.json
        $json3 = <<<'JSON'
{
    "JSON Test Pattern pass3": {
        "The outermost value": "must be an object or array.",
        "In this test": "It is an object."
    }
}
JSON;

        $this->assertTrue(Json::rule()->validate($json1)->isValid());
        $this->assertTrue(Json::rule()->validate($json2)->isValid());
        $this->assertTrue(Json::rule()->validate($json3)->isValid());
    }

    public function testValidationMessage(): void
    {
        $this->assertEquals(['The value is not JSON.'], Json::rule()->validate('')->getErrorMessages());
    }

    public function testCustomValidationMessage(): void
    {
        $this->assertEquals(['bad json'], Json::rule()->message('bad json')->validate('')->getErrorMessages());
    }

    public function testName(): void
    {
        $this->assertEquals('json', Json::rule()->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                Json::rule(),
                [
                    'message' => 'The value is not JSON.',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
