<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Url;

use function function_exists;

/**
 * @group validators
 */
class UrlTest extends TestCase
{
    public static ?bool $idnFunctionExists = null;

    protected function setUp(): void
    {
        static::$idnFunctionExists = null;
        parent::setUp();
    }

    protected function tearDown(): void
    {
        static::$idnFunctionExists = null;
        parent::tearDown();
    }

    public function testValidate(): void
    {
        $val = Url::rule();
        $this->assertFalse($val->validate('google.de')->isValid());
        $this->assertTrue($val->validate('http://google.de')->isValid());
        $this->assertTrue($val->validate('https://google.de')->isValid());
        $this->assertFalse($val->validate('htp://yiiframework.com')->isValid());
        $this->assertTrue($val->validate('https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8'
                                        . '&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr')->isValid());
        $this->assertFalse($val->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertFalse($val->validate('http://invalid,domain')->isValid());
        $this->assertFalse($val->validate('http://example.com,')->isValid());
        $this->assertFalse($val->validate('http://example.com*12')->isValid());
        $this->assertTrue($val->validate('http://example.com/*12')->isValid());
        $this->assertTrue($val->validate('http://example.com/?test')->isValid());
        $this->assertTrue($val->validate('http://example.com/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:80/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:65535/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81/?good')->isValid());
        $this->assertTrue($val->validate('http://example.com?test')->isValid());
        $this->assertTrue($val->validate('http://example.com#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81?good')->isValid());
        $this->assertFalse($val->validate('http://example.com,?test')->isValid());
        $this->assertFalse($val->validate('http://example.com:?test')->isValid());
        $this->assertFalse($val->validate('http://example.com:test')->isValid());
        $this->assertFalse($val->validate('http://example.com:123456/test')->isValid());

        $this->assertFalse($val->validate('http://äüö?=!"§$%&/()=}][{³²€.edu')->isValid());
    }

    public function testValidateWithoutScheme(): void
    {
        $val = Url::rule()
            ->pattern('/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)/');

        $this->assertTrue($val->validate('yiiframework.com')->isValid());
    }

    public function testValidateWithCustomScheme(): void
    {
        $val = Url::rule()
            ->schemes(['http', 'https', 'ftp', 'ftps']);

        $this->assertTrue($val->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertTrue($val->validate('http://google.de')->isValid());
        $this->assertTrue($val->validate('https://google.de')->isValid());
        $this->assertFalse($val->validate('htp://yiiframework.com')->isValid());
        // relative urls not supported
        $this->assertFalse($val->validate('//yiiframework.com')->isValid());
    }

    public function testValidateWithIdn(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
            return;
        }

        $val = Url::rule()->enableIDN();
        $this->assertTrue($val->validate('http://äüößìà.de')->isValid());
        // converted via http://mct.verisign-grs.com/convertServlet
        $this->assertTrue($val->validate('http://xn--zcack7ayc9a.de')->isValid());
    }

    public function testValidateWithIdnType(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
            return;
        }

        $val = Url::rule()->enableIDN();
        $this->assertFalse($val->validate('')->isValid());
    }

    public function testEnableIdnException(): void
    {
        static::$idnFunctionExists = false;

        $this->expectException(RuntimeException::class);
        Url::rule()->enableIDN();
    }

    public function testValidateLength(): void
    {
        $url = 'http://' . str_pad('base', 2000, 'url') . '.de';
        $val = Url::rule();
        $this->assertFalse($val->validate($url)->isValid());
    }

    public function testValidateWithIdnWithoutScheme(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
            return;
        }

        $validator = Url::rule()->pattern('/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i')->enableIDN();
        $this->assertTrue($validator->validate('домен.рф')->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('url', Url::rule()->getName());
    }

    public function optionsProvider(): array
    {
        return [
            'default' => [
                Url::rule(),
                [
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => false,
                    'validSchemes' => ['http', 'https'],
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'IDN enabled' => [
                Url::rule()->enableIDN(),
                [
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => true,
                    'validSchemes' => ['http', 'https'],
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'HTTP scheme only' => [
                Url::rule()->schemes(['http']),
                [
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => false,
                    'validSchemes' => ['http'],
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'custom pattern' => [Url::rule()->pattern('/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/')->enableIDN(),
                [
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => true,
                    'validSchemes' => ['http', 'https'],
                    'pattern' => '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/',
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

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Tests\Rule\UrlTest;

function function_exists($function)
{
    return $function === 'idn_to_ascii' && UrlTest::$idnFunctionExists !== null
        ? UrlTest::$idnFunctionExists
        : \function_exists($function);
}
