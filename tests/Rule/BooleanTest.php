<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\SerializableRuleInterface;

final class BooleanTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Boolean(),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(strict: true, skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => true,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(trueValue: 'YES'),
                [
                    'trueValue' => 'YES',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => 'YES',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(falseValue: 'NO'),
                [
                    'trueValue' => '1',
                    'falseValue' => 'NO',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => 'NO',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(trueValue: 'YES', falseValue: 'NO', strict: true),
                [
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'strict' => true,
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => 'YES',
                            'false' => 'NO',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Boolean([]);
    }
}
