<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class GreaterThanTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new GreaterThan(1);
        $this->assertSame('greaterThan', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new GreaterThan(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be greater than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThan(1, type: CompareType::NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be greater than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '>',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThan(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be greater than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new GreaterThan(
                    targetAttribute: 'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                ),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '>',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [100, [new GreaterThan(99)]],
            ['100', [new GreaterThan('99')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be greater than "100".';

        return [
            [99, [new GreaterThan(100)], ['' => [$message]]],
            ['100', [new GreaterThan(100)], ['' => [$message]]],
            'custom error' => [99, [new GreaterThan(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new GreaterThan();
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new GreaterThan(1), new GreaterThan(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new GreaterThan(1), new GreaterThan(1, when: $when));
    }
}
