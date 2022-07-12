<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\ParametrizedRuleInterface;

final class EqualTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Equal(1),
                [
                    'equalValue' => 1,
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 1,
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal(1, type: Equal::TYPE_NUMBER),
                [
                    'equalValue' => 1,
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 1,
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal(1, type: Equal::TYPE_NUMBER),
                [
                    'equalValue' => 1,
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 1,
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal('YES'),
                [
                    'equalValue' => 'YES',
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 'YES',
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal('YES', strict: true),
                [
                    'equalValue' => 'YES',
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 'YES',
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 'YES'
                        ],
                    ],
                    'type' => 'string',
                    'strict' => true,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal('YES', skipOnEmpty: true),
                [
                    'equalValue' => 'YES',
                    'equalAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => 'YES',
                            'equalAttribute' => null,
                            'equalValueOrAttribute' => 'YES'
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal(null, 'attribute'),
                [
                    'equalValue' => null,
                    'equalAttribute' => 'attribute',
                    'message' => [
                        'message' => 'Value must be equal to "{equalValueOrAttribute}".',
                        'parameters' => [
                            'equalValue' => null,
                            'equalAttribute' => 'attribute',
                            'equalValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal(equalAttribute: 'test', message: 'Custom message for {equalValueOrAttribute}'),
                [
                    'equalValue' => null,
                    'equalAttribute' => 'test',
                    'message' => [
                        'message' => 'Custom message for {equalValueOrAttribute}',
                        'parameters' => [
                            'equalValue' => null,
                            'equalAttribute' => 'test',
                            'equalValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Equal(1);
    }
}
