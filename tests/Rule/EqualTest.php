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
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
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
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
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
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
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
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
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
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES'
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
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES'
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
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'strict' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Equal(targetAttribute: 'test', message: 'Custom message for {targetValueOrAttribute}'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'message' => [
                        'message' => 'Custom message for {targetValueOrAttribute}',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
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
