<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
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
        $this->assertSame(GreaterThan::class, $rule->getName());
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
                        'template' => 'The allowed types are integer, float, string, boolean, null and object ' .
                            'implementing \Stringable interface or \DateTimeInterface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have one of the ' .
                            'following types: integer, float, string, boolean, null or an object implementing ' .
                            '\Stringable interface or \DateTimeInterface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => '{label} must be greater than "{targetValueOrAttribute}".',
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
                new GreaterThan(
                    new DateTime('2023-02-07 12:57:12'),
                    targetAttribute: 'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                    type: CompareType::ORIGINAL,
                    skipOnEmpty: true,
                    skipOnError: true,
                    when: static fn (): bool => true,
                ),
                [
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'type' => 'original',
                    'operator' => '>',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
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
        $message = 'The value must be greater than "100".';

        return [
            [99, [new GreaterThan(100)], ['' => [$message]]],
            ['100', [new GreaterThan(100)], ['' => [$message]]],
            'custom error' => [99, [new GreaterThan(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
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
