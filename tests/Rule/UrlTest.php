<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;

final class UrlTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Url();
        $this->assertSame('url', $rule->getName());
    }

    public function dataOptions(): array
    {
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

    /**
     * @requires extension intl
     * @dataProvider dataOptions
     */
    public function testOptions(Url $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
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

    /**
     * @requires extension intl
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
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
        ];
    }

    /**
     * @requires extension intl
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    /**
     * @requires extension intl
     */
    public function testCustomErrorMessage(): void
    {
        $data = '';
        $rules = [new Url(enableIDN: true, message: 'Custom error')];

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(UrlHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Url::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }
}
