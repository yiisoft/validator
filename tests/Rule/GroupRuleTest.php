<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\Tests\FunctionExists;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

final class GroupRuleTest extends AbstractRuleTest
{
    protected function setUp(): void
    {
        FunctionExists::$isIdnFunctionExists = true;
        parent::setUp();
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new CustomUrlRule(),
                [
                    [
                        'required',
                        'message' => [
                            'message' => 'Value cannot be blank.',
                        ],
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                    ],
                    [
                        'url',
                        'pattern' => '/^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                        'validSchemes' => ['http', 'https',],
                        'enableIDN' => true,
                        'message' => [
                            'message' => 'This value is not a valid URL.',
                        ],
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                    ],
                    [
                        'hasLength',
                        'min' => null,
                        'max' => 20,
                        'message' => [
                            'message' => 'This value must be a string.',
                        ],
                        'tooShortMessage' => [
                            'message' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                            'parameters' => ['min' => null],
                        ],
                        'tooLongMessage' => [
                            'message' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                            'parameters' => ['max' => 20],
                        ],
                        'encoding' => 'UTF-8',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new CustomUrlRule();
    }
}
