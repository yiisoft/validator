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
        $rule = new Url();
        $this->assertFalse($rule->validate('google.de')->isValid());
        $this->assertTrue($rule->validate('http://google.de')->isValid());
        $this->assertTrue($rule->validate('https://google.de')->isValid());
        $this->assertFalse($rule->validate('htp://yiiframework.com')->isValid());
        $this->assertTrue($rule->validate('https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8'
                                        . '&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr')->isValid());
        $this->assertFalse($rule->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertFalse($rule->validate('http://invalid,domain')->isValid());
        $this->assertFalse($rule->validate('http://example.com,')->isValid());
        $this->assertFalse($rule->validate('http://example.com*12')->isValid());
        $this->assertTrue($rule->validate('http://example.com/*12')->isValid());
        $this->assertTrue($rule->validate('http://example.com/?test')->isValid());
        $this->assertTrue($rule->validate('http://example.com/#test')->isValid());
        $this->assertTrue($rule->validate('http://example.com:80/#test')->isValid());
        $this->assertTrue($rule->validate('http://example.com:65535/#test')->isValid());
        $this->assertTrue($rule->validate('http://example.com:81/?good')->isValid());
        $this->assertTrue($rule->validate('http://example.com?test')->isValid());
        $this->assertTrue($rule->validate('http://example.com#test')->isValid());
        $this->assertTrue($rule->validate('http://example.com:81#test')->isValid());
        $this->assertTrue($rule->validate('http://example.com:81?good')->isValid());
        $this->assertFalse($rule->validate('http://example.com,?test')->isValid());
        $this->assertFalse($rule->validate('http://example.com:?test')->isValid());
        $this->assertFalse($rule->validate('http://example.com:test')->isValid());
        $this->assertFalse($rule->validate('http://example.com:123456/test')->isValid());
        $this->assertFalse($rule->validate('http://äüö?=!"§$%&/()=}][{³²€.edu')->isValid());
    }

    public function testValidateWithoutScheme(): void
    {
        $rule = new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)/');
        $this->assertTrue($rule->validate('yiiframework.com')->isValid());
    }

    public function testValidateWithCustomScheme(): void
    {
        $rule = new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']);

        $this->assertTrue($rule->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertTrue($rule->validate('http://google.de')->isValid());
        $this->assertTrue($rule->validate('https://google.de')->isValid());
        $this->assertFalse($rule->validate('htp://yiiframework.com')->isValid());
        // relative urls are not supported
        $this->assertFalse($rule->validate('//yiiframework.com')->isValid());
    }

    public function testSchemaShouldBeCaseInsensitive(): void
    {
        $val = new Url(validSchemes: ['http', 'FTP']);

        $this->assertTrue($val->validate('HtTp://www.yiiframework.com/')->isValid());
        $this->assertTrue($val->validate('fTp://www.yiiframework.com/')->isValid());
    }

    public function testValidateWithIdn(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
        }

        $rule = new Url(enableIDN: true);
        $this->assertTrue($rule->validate('http://äüößìà.de')->isValid());
        // converted via http://mct.verisign-grs.com/convertServlet
        $this->assertTrue($rule->validate('http://xn--zcack7ayc9a.de')->isValid());
    }

    public function testValidateWithIdnType(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
        }

        $rule = new Url(enableIDN: true);
        $this->assertFalse($rule->validate('')->isValid());
    }

    public function testEnableIdnException(): void
    {
        static::$idnFunctionExists = false;

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    public function testValidateLength(): void
    {
        $url = 'http://' . str_pad('base', 2000, 'url') . '.de';
        $rule = new Url();

        $this->assertFalse($rule->validate($url)->isValid());
    }

    public function testValidateWithIdnWithoutScheme(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
        }

        $rule = new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIDN: true);
        $this->assertTrue($rule->validate('домен.рф')->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('url', (new Url())->getName());
    }

    public function optionsProvider(): array
    {
        return [
            'default' => [
                new Url(),
                [
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => false,
                    'message' => 'This value is not a valid URL.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'IDN enabled' => [
                new Url(enableIDN: true),
                [
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => true,
                    'message' => 'This value is not a valid URL.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'HTTP scheme only' => [
                new Url(validSchemes: ['http']),
                [
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http'],
                    'enableIDN' => false,
                    'message' => 'This value is not a valid URL.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom pattern' => [
                new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/', enableIDN: true),
                [
                    'pattern' => '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => true,
                    'message' => 'This value is not a valid URL.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
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
