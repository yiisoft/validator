<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class LessThanOrEqualTest extends RuleTestCase
{
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new LessThanOrEqual(1);
        $this->assertSame('lessThanOrEqual', $rule->getName());
    }

    public function dataOptions(): array
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

    public function dataValidationPassed(): array
    {
        return [
            [100, [new LessThanOrEqual(101)]],
            [100, [new LessThanOrEqual(100)]],
            ['100', [new LessThanOrEqual('101')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be less than or equal to "100".';

        return [
            [101, [new LessThanOrEqual(100)], ['' => [$message]]],
            ['101', [new LessThanOrEqual(100)], ['' => [$message]]],
            'custom error' => [101, [new LessThanOrEqual(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new LessThanOrEqual();
    }
}
