<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Url;

use RuntimeException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Rule\Url\Url;
use Yiisoft\Validator\Rule\Url\UrlHandler;
use Yiisoft\Validator\Tests\FunctionExists;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class UrlHandlerTest extends AbstractRuleValidatorTest
{
    protected function setUp(): void
    {
        FunctionExists::$isIdnFunctionExists = true;
        parent::setUp();
    }

    public function failedValidationProvider(): array
    {
        $rule = new Url();
        $errors = [new Error($rule->message, [])];

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

            [new Url(enableIDN: true), 'http://äüößìà.de'],
            [new Url(enableIDN: true), 'http://xn--zcack7ayc9a.de'],
            [new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIDN: true), 'домен.рф'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Url(enableIDN: true, message: 'Custom error'), '', [new Error('Custom error', [])]],
        ];
    }

    public function testEnableIdnException(): void
    {
        FunctionExists::$isIdnFunctionExists = false;

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new UrlHandler();
    }
}
