<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\ValidationContext;

final class EqualTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Equal(1);
        $this->assertSame('equal', $rule->getName());
    }

    public function dataOptions(): array
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

    public function dataValidationPassed(): array
    {
        return [
            [100, [new Equal(100)]],
            ['100', [new Equal(100)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be equal to "100".';

        return [
            [101, [new Equal(100)], ['' => [$message]]],
            [101, [new Equal(100, strict: true)], ['' => [$message]]],
            'custom error' => [101, [new Equal(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new Equal();
    }

    public function testSkipOnError(): void
    {
        $this->testskipOnErrorInternal(new Equal(1), new Equal(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Equal(1), new Equal(1, when: $when));
    }
}
