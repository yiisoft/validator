<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use RuntimeException;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\ValidationContext;

final class LessThanTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new LessThan(1);
        $this->assertSame('lessThan', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new LessThan(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThan(1, type: LessThan::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThan(1, type: LessThan::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThan(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'message' => [
                        'message' => 'Value must be less than "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThan(targetAttribute: 'test', message: 'Custom message for {targetValueOrAttribute}'),
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
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [100, [new LessThan(101)]],
            ['100', [new LessThan('101')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            [100, [new LessThan(100)], ['' => ['Value must be less than "100".']]],
            ['101', [new LessThan(100)], ['' => ['Value must be less than "100".']]],
            'custom error' => [101, [new LessThan(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new LessThan();
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new LessThan(1), new LessThan(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new LessThan(1), new LessThan(1, when: $when));
    }
}
