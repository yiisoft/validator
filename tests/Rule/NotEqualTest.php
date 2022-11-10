<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class NotEqualTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new NotEqual(1);
        $this->assertSame('notEqual', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new NotEqual(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual(1, type: NotEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual('YES'),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual('YES', strict: true),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual('YES', skipOnEmpty: true),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!=',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new NotEqual(targetAttribute: 'test', scalarMessage: 'Custom message for {targetValueOrAttribute}.'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                    ],
                    'nonScalarMessage' => [
                        'message' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
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
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [101, [new NotEqual(100)]],
            ['101', [new NotEqual(100, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $scalarMessage = 'The scalar value must not be equal to "100".';

        return [
            [100, [new NotEqual(100)], ['' => [$scalarMessage]]],
            [100, [new NotEqual(100, strict: true)], ['' => [$scalarMessage]]],
            'custom error' => [100, [new NotEqual(100, scalarMessage: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new NotEqual();
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new NotEqual(1), new NotEqual(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new NotEqual(1), new NotEqual(1, when: $when));
    }
}
