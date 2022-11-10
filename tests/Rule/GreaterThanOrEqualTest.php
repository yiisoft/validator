<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class GreaterThanOrEqualTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new GreaterThanOrEqual(1);
        $this->assertSame('greaterThanOrEqual', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new GreaterThanOrEqual(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThanOrEqual(1, type: GreaterThanOrEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThanOrEqual(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThanOrEqual(
                    targetAttribute: 'test',
                    scalarMessage: 'Custom message for {targetValueOrAttribute}.',
                ),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'Custom message for {targetValueOrAttribute}.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [100, [new GreaterThanOrEqual(99)]],
            ['100', [new GreaterThanOrEqual('100')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $scalarMessage = 'The scalar value must be greater than or equal to "100".';

        return [
            [99, [new GreaterThanOrEqual(100)], ['' => [$scalarMessage]]],
            ['99', [new GreaterThanOrEqual(100)], ['' => [$scalarMessage]]],
            'custom error' => [
                99,
                [new GreaterThanOrEqual(100, scalarMessage: 'Custom error')],
                ['' => ['Custom error']],
            ],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new GreaterThanOrEqual();
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new GreaterThanOrEqual(1), new GreaterThanOrEqual(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new GreaterThanOrEqual(1), new GreaterThanOrEqual(1, when: $when));
    }
}
