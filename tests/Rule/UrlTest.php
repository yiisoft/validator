<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Url;

use function function_exists;

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

    public function validateWithDefaultArgumentsProvider(): array
    {
        return [
            ['google.de', false],
            ['http://google.de', true],
            ['https://google.de', true],
            ['htp://yiiframework.com', false],
            [
                'https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8&rls=org.mozilla:de:official'
                . '&client=firefox-a&gws_rd=cr',
                true,
            ],

            ['ftp://ftp.ruhr-uni-bochum.de/', false],
            ['http://invalid,domain', false],
            ['http://example.com,', false],
            ['http://example.com*12', false],
            ['http://example.com/*12', true],
            ['http://example.com/?test', true],
            ['http://example.com/#test', true],
            ['http://example.com:80/#test', true],
            ['http://example.com:65535/#test', true],
            ['http://example.com:81/?good', true],
            ['http://example.com?test', true],
            ['http://example.com#test', true],
            ['http://example.com:81#test', true],
            ['http://example.com:81?good', true],
            ['http://example.com,?test', false],
            ['http://example.com:?test', false],
            ['http://example.com:test', false],
            ['http://example.com:123456/test', false],
            ['http://äüö?=!"§$%&/()=}][{³²€.edu', false],
        ];
    }

    /**
     * @dataProvider validateWithDefaultArgumentsProvider
     */
    public function testValidateWithDefaultArguments(string $value, bool $expectedIsValid): void
    {
        $rule = new Url();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testValidateWithoutScheme(): void
    {
        $rule = new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)/');
        $result = $rule->validate('yiiframework.com');

        $this->assertTrue($result->isValid());
    }

    public function validateWithCustomSchemeProvider(): array
    {
        $rule = new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']);

        return [
            [$rule, 'ftp://ftp.ruhr-uni-bochum.de/', true],
            [$rule, 'http://google.de', true],
            [$rule, 'https://google.de', true],
            [$rule, 'htp://yiiframework.com', false],
            [$rule, '//yiiframework.com', false], // Relative URLs are not supported
        ];
    }

    /**
     * @dataProvider validateWithCustomSchemeProvider
     */
    public function testValidateWithCustomScheme(Url $rule, string $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function schemaShouldBeCaseInsensitiveProvider(): array
    {
        $rule = new Url(validSchemes: ['http', 'FTP']);

        return [
            [$rule, 'HtTp://www.yiiframework.com/'],
            [$rule, 'fTp://www.yiiframework.com/'],
        ];
    }

    /**
     * @dataProvider schemaShouldBeCaseInsensitiveProvider
     */
    public function testSchemaShouldBeCaseInsensitive(Url $rule, string $value): void
    {
        $result = $rule->validate($value);
        $this->assertTrue($result->isValid());
    }

    public function validateWithIdnProvider(): array
    {
        return [
            ['http://äüößìà.de'],
            ['http://xn--zcack7ayc9a.de'],
        ];
    }

    /**
     * @dataProvider validateWithIdnProvider
     */
    public function testValidateWithIdn(string $value): void
    {
        $this->intlPackageRequired();

        $rule = new Url(enableIDN: true);
        $result = $rule->validate($value);

        $this->assertTrue($result->isValid());
    }

    public function testValidateWithIdnType(): void
    {
        $this->intlPackageRequired();

        $rule = new Url(enableIDN: true);
        $result = $rule->validate('');

        $this->assertFalse($result->isValid());
    }

    public function testEnableIdnException(): void
    {
        static::$idnFunctionExists = false;

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    public function testValidateLength(): void
    {
        $value = 'http://' . str_pad('base', 2000, 'url') . '.de';
        $rule = new Url();
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function testValidateWithIdnWithoutScheme(): void
    {
        $this->intlPackageRequired();

        $rule = new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIDN: true);
        $result = $rule->validate('домен.рф');

        $this->assertTrue($result->isValid());
    }

    public function testGetName(): void
    {
        $rule = new Url();
        $this->assertSame('url', $rule->getName());
    }

    public function getOptionsProvider(): array
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
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Url $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }

    private function intlPackageRequired(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');
        }
    }
}

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Tests\Rule\UrlTest;

function function_exists(string $function): bool
{
    return $function === 'idn_to_ascii' && UrlTest::$idnFunctionExists !== null
        ? UrlTest::$idnFunctionExists
        : \function_exists($function);
}
