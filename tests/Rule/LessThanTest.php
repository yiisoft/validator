<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class LessThanTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new LessThan(1);
        $this->assertSame(LessThan::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new LessThan(1),
                [
                    'targetValue' => 1,
                    'targetProperty' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean, null and object ' .
                            'implementing \Stringable interface or \DateTimeInterface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have one of the ' .
                            'following types: integer, float, string, boolean, null or an object implementing ' .
                            '\Stringable interface or \DateTimeInterface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => '{Property} must be less than "{targetValueOrProperty}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThan(
                    new DateTime('2023-02-07 12:57:12'),
                    targetProperty: 'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                    type: CompareType::ORIGINAL,
                    skipOnEmpty: true,
                    skipOnError: true,
                    when: static fn (): bool => true,
                ),
                [
                    'targetProperty' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'type' => 'original',
                    'operator' => '<',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
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
        $message = 'Value must be less than "100".';

        return [
            [100, [new LessThan(100)], ['' => [$message]]],
            ['101', [new LessThan(100)], ['' => [$message]]],
            'custom error' => [101, [new LessThan(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
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
