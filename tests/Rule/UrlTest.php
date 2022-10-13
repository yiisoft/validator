<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;

final class UrlTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use DifferentRuleInHandlerTestTrait;

    public function testGetName(): void
    {
        $rule = new Url();
        $this->assertSame('url', $rule->getName());
    }

    public function dataOptions(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        return [
            [
                new Url(),
                [
                    'pattern' => '/^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid URL.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Url(enableIDN: true),
                [
                    'pattern' => '/^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => true,
                    'message' => [
                        'message' => 'This value is not a valid URL.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Url(validSchemes: ['http']),
                [
                    'pattern' => '/^((?i)http):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http'],
                    'enableIDN' => false,
                    'message' => [
                        'message' => 'This value is not a valid URL.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/', enableIDN: true),
                [
                    'pattern' => '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/',
                    'validSchemes' => ['http', 'https'],
                    'enableIDN' => true,
                    'message' => [
                        'message' => 'This value is not a valid URL.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function beforeTestOptions(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }
    }

    public function dataValidationPassed(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        return [
            ['http://google.de', [new Url()]],
            ['https://google.de', [new Url()]],
            [
                'https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr',
                [new Url()],
            ],
            ['http://example.com/*12', [new Url()]],
            ['http://example.com/?test', [new Url()]],
            ['http://example.com/#test', [new Url()]],
            ['http://example.com:80/#test', [new Url()]],
            ['http://example.com:65535/#test', [new Url()]],
            ['http://example.com:81/?good', [new Url()]],
            ['http://example.com?test', [new Url()]],
            ['http://example.com#test', [new Url()]],
            ['http://example.com:81#test', [new Url()]],
            ['http://example.com:81?good', [new Url()]],

            ['yiiframework.com', [new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)/')]],

            ['ftp://ftp.ruhr-uni-bochum.de/', [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps'])]],
            ['http://google.de', [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps'])]],
            ['https://google.de', [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps'])]],

            ['HtTp://www.yiiframework.com/', [new Url(validSchemes: ['http', 'FTP'])]],
            ['fTp://www.yiiframework.com/', [new Url(validSchemes: ['http', 'FTP'])]],

            ['http://äüößìà.de', [new Url(enableIDN: true)]],
            ['http://xn--zcack7ayc9a.de', [new Url(enableIDN: true)]],
            ['домен.рф', [new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIDN: true)]],
        ];
    }

    public function beforeTestValidationPassed(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }
    }

    public function dataValidationFailed(): array
    {
        if (!extension_loaded('intl')) {
            return [];
        }

        $errors = ['' => ['This value is not a valid URL.']];

        return [
            ['google.de', [new Url()], $errors],
            ['htp://yiiframework.com', [new Url()], $errors],
            ['ftp://ftp.ruhr-uni-bochum.de/', [new Url()], $errors],
            ['http://invalid,domain', [new Url()], $errors],
            ['http://example.com,', [new Url()], $errors],
            ['http://example.com*12', [new Url()], $errors],
            ['http://example.com,?test', [new Url()], $errors],
            ['http://example.com:?test', [new Url()], $errors],
            ['http://example.com:test', [new Url()], $errors],
            ['http://example.com:123456/test', [new Url()], $errors],
            ['http://äüö?=!"§$%&/()=}][{³²€.edu', [new Url()], $errors],


            ['htp://yiiframework.com', [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps'])], $errors],
            // Relative URLs are not supported
            ['//yiiframework.com', [new Url(validSchemes: ['http', 'https', 'ftp', 'ftps'])], $errors],

            ['', [new Url(enableIDN: true)], $errors],
            ['http://' . str_pad('base', 2000, 'url') . '.de', [new Url(enableIDN: true)], $errors],

            'custom error' => ['', [new Url(enableIDN: true, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function beforeTestValidationFailed(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Url::class, UrlHandler::class];
    }
}
