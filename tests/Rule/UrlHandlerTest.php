<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Xepozz\InternalMocker\MockerState;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\Tests\MockerExtension;

final class UrlHandlerTest extends AbstractRuleValidatorTest
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        MockerExtension::load();
        parent::__construct($name, $data, $dataName);
    }

    public function failedValidationProvider(): array
    {
        $rule = new Url();
        $errors = [new Error('This value is not a valid URL.')];

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
            // Relative URLs are not supported
            [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']), '//yiiframework.com', $errors],

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

    /**
     * @dataProvider passedValidationProvider
     */
    public function testValidationPassed(object $config, mixed $value): void
    {
        /**
         * @var $config Url
         */
        if ($config->isEnableIDN()) {
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['äüößìà.de', 0, 1],
                'xn--ss-y1a5b0g1frd.de',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['xn--zcack7ayc9a.de', 0, 1],
                'xn--zcack7ayc9a.de',
            );
            MockerState::addCondition(
                'Yiisoft\\Validator\\Rule',
                'idn_to_ascii',
                ['домен.рф', 0, 1],
                'xn--d1acufc.xn--p1ai1111',
            );
        }

        parent::testValidationPassed($config, $value);
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Url(enableIDN: true, message: 'Custom error'), '', [new Error('Custom error')]],
        ];
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        MockerState::addCondition(
            'Yiisoft\\Validator\\Rule',
            'function_exists',
            ['idn_to_ascii'],
            false,
        );
        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new UrlHandler($this->getTranslator());
    }
}
