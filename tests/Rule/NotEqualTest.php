<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class NotEqualTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
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
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                new NotEqual(1, type: CompareType::NUMBER),
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
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                new NotEqual(
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
        $message = 'Value must not be equal to "100".';

        return [
            [100, [new NotEqual(100)], ['' => [$message]]],
            [100, [new NotEqual(100, strict: true)], ['' => [$message]]],
            'custom error' => [100, [new NotEqual(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
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
