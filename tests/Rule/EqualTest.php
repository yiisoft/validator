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
                    'operator' => '==',
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
                    'operator' => '==',
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
                    'operator' => '==',
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
                    'operator' => '==',
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
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '===',
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
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
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
                    'operator' => '==',
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
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function testWithoutParameters()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');

        $rule = new Equal();
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Equal(1);
    }
}
