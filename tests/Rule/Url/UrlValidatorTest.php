<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Url;

use RuntimeException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Rule\Url\Url;
use Yiisoft\Validator\Rule\Url\UrlValidator;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t2
 */
final class UrlValidatorTest extends AbstractRuleValidatorTest
{
    public static bool $idnFunctionExists = false;

//    protected function setUp(): void
//    {
//        static::$idnFunctionExists = true;
//        parent::setUp();
//    }
//
//    protected function tearDown(): void
//    {
//        static::$idnFunctionExists = true;
//        parent::tearDown();
//    }

    public function failedValidationProvider(): array
    {
        $rule = new Url();
        $errors = [new Error($rule->message)];

        return [
            [$rule, 'google.de', $errors],
            [$rule, 'htp://yiiframework.com', $errors],
            [$rule, 'ftp://ftp.ruhr-uni-bochum.de/', $errors],
            [$rule, 'http://invalid,domain', $errors],
            [$rule, 'http://example.com,', $errors],
            [$rule, 'http://example.com*12', $errors],
            [$rule, 'http://example.com,?test', $errors],
            [$rule, 'http://example.com:?test', $errors],
            [$rule, 'http://example.com:test', $errors],
            [$rule, 'http://example.com:123456/test', $errors],
            [$rule, 'http://äüö?=!"§$%&/()=}][{³²€.edu', $errors],


            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), 'htp://yiiframework.com', $errors],
            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), '//yiiframework.com', $errors], // Relative URLs are not supported

            // TODO: use mock for intl functions
            [new Url(enableIDN: true), '', $errors],
            [new Url(enableIDN: true), 'http://' . str_pad('base', 2000, 'url') . '.de', $errors],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Url();

        return [
            [$rule, 'http://google.de'],
            [$rule, 'https://google.de'],
            [$rule, 'https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr'],
            [$rule, 'http://example.com/*12'],
            [$rule, 'http://example.com/?test'],
            [$rule, 'http://example.com/#test'],
            [$rule, 'http://example.com:80/#test'],
            [$rule, 'http://example.com:65535/#test'],
            [$rule, 'http://example.com:81/?good'],
            [$rule, 'http://example.com?test'],
            [$rule, 'http://example.com#test'],
            [$rule, 'http://example.com:81#test'],
            [$rule, 'http://example.com:81?good'],

            [new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)/'), 'yiiframework.com'],

            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), 'ftp://ftp.ruhr-uni-bochum.de/'],
            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), 'http://google.de'],
            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), 'https://google.de'],

            [new Url(validSchemes: ['http', 'FTP']), 'HtTp://www.yiiframework.com/'],
            [new Url(validSchemes: ['http', 'FTP']), 'fTp://www.yiiframework.com/'],

            // TODO: use mock for intl functions
            [new Url(enableIDN: true), 'http://äüößìà.de'],
            [new Url(enableIDN: true), 'http://xn--zcack7ayc9a.de'],
            [new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIDN: true), 'домен.рф'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
        ];
    }

    public function testEnableIdnException(): void
    {
        $this->markTestIncomplete('Fix mock');
        self::$idnFunctionExists = false;

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new UrlValidator();
    }

    protected function getConfigClassName(): string
    {
        return Url::class;
    }
}

namespace Yiisoft\Validator\Rule\Url;

use Yiisoft\Validator\Tests\Rule\Url\UrlValidatorTest;

function function_exists(string $function): bool
{
    if ($function === 'idn_to_ascii') {
        return UrlValidatorTest::$idnFunctionExists;
    }
    return \function_exists($function);
}
