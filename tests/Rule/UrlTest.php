<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\SerializableRuleInterface;

final class UrlTest extends AbstractRuleTest
{
    public function testGetName(): void
    {
        $rule = new Url();
        $this->assertSame('url', $rule->getName());
    }

    public function optionsDataProvider(): array
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

    /**
     * @requires extension intl
     * @dataProvider optionsDataProvider
     */
    public function testOptions(SerializableRuleInterface $rule, array $expectedOptions): void
    {
        parent::testOptions($rule, $expectedOptions);
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Url();
    }
}
