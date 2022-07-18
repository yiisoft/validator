<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\SerializableRuleInterface;

final class LessThanOrEqualTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new LessThanOrEqual(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(1, type: LessThanOrEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(1, type: LessThanOrEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(targetAttribute: 'test', message: 'Custom message for {targetValueOrAttribute}'),
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
                    'operator' => '<=',
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

        $rule = new LessThanOrEqual();
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new LessThanOrEqual(1);
    }
}
