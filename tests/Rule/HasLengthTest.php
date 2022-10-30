<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\HasLengthHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\LimitTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class HasLengthTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use LimitTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new HasLength(min: 3);
        $this->assertSame('hasLength', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new HasLength(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(max: 3),
                [
                    'min' => null,
                    'max' => 3,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 3],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(min: 3, max: 4, encoding: 'windows-1251'),
                [
                    'min' => 3,
                    'max' => 4,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 4],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [str_repeat('x', 25), [new HasLength(exactly: 25)]],
            [str_repeat('€', 25), [new HasLength(exactly: 25)]],

            [str_repeat('x', 125), [new HasLength(min: 25)]],
            [str_repeat('€', 25), [new HasLength(min: 25)]],

            [str_repeat('x', 25), [new HasLength(max: 25)]],
            [str_repeat('Ä', 24), [new HasLength(max: 25)]],
            ['', [new HasLength(max: 25)]],

            [str_repeat('x', 15), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 10), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 20), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 25), [new HasLength(min: 10, max: 25)]],

            [str_repeat('x', 5), [new HasLength(min: 1)]],
            [str_repeat('x', 5), [new HasLength(max: 100)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'This value must be a string.';
        $greaterThanMaxMessage = 'This value must contain at most 25 characters.';
        $notExactlyMessage = 'This value must contain exactly 25 characters.';
        $lessThanMinMessage = 'This value must contain at least 25 characters.';

        $customErrorRules = [
            new HasLength(
                min: 3,
                max: 5,
                message: 'is not string error',
                lessThanMinMessage: 'is too short test',
                greaterThanMaxMessage: 'is too long test'
            ),
        ];

        return [
            [['not a string'], [new HasLength(min: 25)], ['' => [$message]]],
            [new SingleValueDataSet(new stdClass()), [new HasLength(min: 25)], ['' => [$message]]],
            [true, [new HasLength(min: 25)], ['' => [$message]]],
            [false, [new HasLength(min: 25)], ['' => [$message]]],

            [str_repeat('x', 1250), [new HasLength(max: 25)], ['' => [$greaterThanMaxMessage]]],
            [str_repeat('x', 125), [new HasLength(exactly: 25)], ['' => [$notExactlyMessage]]],

            ['', [new HasLength(exactly: 25)], ['' => [$notExactlyMessage]]],
            [
                str_repeat('x', 5),
                [new HasLength(min: 10, max: 25)],
                ['' => ['This value must contain at least 10 characters.']],
            ],
            [str_repeat('x', 13), [new HasLength(min: 25)], ['' => [$lessThanMinMessage]]],
            ['', [new HasLength(min: 25)], ['' => [$lessThanMinMessage]]],

            [null, $customErrorRules, ['' => ['is not string error']]],
            [str_repeat('x', 1), $customErrorRules, ['' => ['is too short test']]],
            [str_repeat('x', 6), $customErrorRules, ['' => ['is too long test']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new HasLength(min: 3), new HasLength(min: 3, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new HasLength(min: 3), new HasLength(min: 3, when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [HasLength::class, HasLengthHandler::class];
    }

    protected function getRuleClass(): string
    {
        return HasLength::class;
    }
}
