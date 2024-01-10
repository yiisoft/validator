<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class UrlTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testNumberEmptyPattern(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pattern can\'t be empty.');
        new Url(pattern: '');
    }

    public function testDefaultValues(): void
    {
        $rule = new Url();

        $this->assertSame(Url::class, $rule->getName());
        $this->assertSame(['http', 'https'], $rule->getValidSchemes());
    }

    public function testGetValidSchemes(): void
    {
        $rule = new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']);
        $this->assertSame(['http', 'https', 'ftp', 'ftps'], $rule->getValidSchemes());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Url(),
                [
                    'pattern' => '/^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIdn' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Attribute} must be a string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Attribute} is not a valid URL.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Url(enableIdn: true),
                [
                    'pattern' => '/^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'validSchemes' => ['http', 'https'],
                    'enableIdn' => true,
                    'incorrectInputMessage' => [
                        'template' => '{Attribute} must be a string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Attribute} is not a valid URL.',
                        'parameters' => [],
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
                    'enableIdn' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Attribute} must be a string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Attribute} is not a valid URL.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Url(pattern: '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/', enableIdn: true),
                [
                    'pattern' => '/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+).*$/',
                    'validSchemes' => ['http', 'https'],
                    'enableIdn' => true,
                    'incorrectInputMessage' => [
                        'template' => '{Attribute} must be a string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Attribute} is not a valid URL.',
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

            ['http://äüößìà.de', [new Url(enableIdn: true)]],
            ['http://xn--zcack7ayc9a.de', [new Url(enableIdn: true)]],
            ['домен.рф', [new Url(pattern: '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', enableIdn: true)]],

            ['http://' . str_repeat('a', 1989) . '.de', [new Url()]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputErrors = ['' => ['Value must be a string.']];
        $errors = ['' => ['Value is not a valid URL.']];
        $longUrl = 'http://' . str_repeat('u', 1990) . '.de';

        return [
            'incorrect input, integer' => [1, [new Url()], $incorrectInputErrors],
            'incorrect input, string in array' => [['yiiframework.com'], [new Url()], $incorrectInputErrors],
            'incorrect input, object' => [new stdClass(), [new Url()], $incorrectInputErrors],
            'custom incorrect input message' => [
                1,
                [new Url(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Url(incorrectInputMessage: 'Attribute - {Attribute}, type - {type}.')],
                ['' => ['Attribute - Value, type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['attribute' => 1],
                ['attribute' => [new Url(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')]],
                ['attribute' => ['Attribute - attribute, type - int.']],
            ],

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

            ['', [new Url(enableIdn: true)], $errors],
            [$longUrl, [new Url(enableIdn: true)], $errors],
            [$longUrl, [new Url()], $errors],

            'custom message' => [
                '',
                [new Url(enableIdn: true, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                'not a url',
                [new Url(enableIdn: true, message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - value, value - not a url.']],
            ],
            'custom message with parameters, attribute set' => [
                ['attribute' => 'not a url'],
                ['attribute' => new Url(enableIdn: true, message: 'Attribute - {attribute}, value - {value}.')],
                ['attribute' => ['Attribute - attribute, value - not a url.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Url(), new Url(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Url(), new Url(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Url::class, UrlHandler::class];
    }
}
