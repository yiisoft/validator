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
                    'incorrectInputMessage' => [
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
                    'incorrectInputMessage' => [
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
                    'incorrectInputMessage' => [
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
        $incorrectInputMessage = 'This value must be a string.';
        $greaterThanMaxMessage = 'This value must contain at most 25 characters.';
        $notExactlyMessage = 'This value must contain exactly 25 characters.';
        $lessThanMinMessage = 'This value must contain at least 25 characters.';

        return [
            'incorrect input, array' => [['not a string'], [new HasLength(min: 25)], ['' => [$incorrectInputMessage]]],
            'incorrect input, boolean (true)' => [true, [new HasLength(min: 25)], ['' => [$incorrectInputMessage]]],
            'incorrect input, boolean (false)' => [false, [new HasLength(min: 25)], ['' => [$incorrectInputMessage]]],
            'custom incorrect input message' => [
                false,
                [new HasLength(min: 25, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                false,
                [new HasLength(min: 25, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - bool.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => false],
                ['data' => new HasLength(min: 25, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - bool.']],
            ],

            [new SingleValueDataSet(new stdClass()), [new HasLength(min: 25)], ['' => [$incorrectInputMessage]]],

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

            'custom less than min message' => [
                'ab',
                [new HasLength(min: 3, lessThanMinMessage: 'Custom less than min message.')],
                ['' => ['Custom less than min message.']],
            ],
            'custom less than min message with parameters' => [
                'ab',
                [new HasLength(min: 3, lessThanMinMessage: 'Min - {min}, attribute - {attribute}, number - {number}.')],
                ['' => ['Min - 3, attribute - , number - 2.']],
            ],
            'custom less than min message with parameters, attribute set' => [
                ['data' => 'ab'],
                [
                    'data' => new HasLength(
                        min: 3,
                        lessThanMinMessage: 'Min - {min}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['data' => ['Min - 3, attribute - data, number - 2.']],
            ],

            'custom greater than max message' => [
                'abcd',
                [new HasLength(max: 3, greaterThanMaxMessage: 'Custom greater than max message.')],
                ['' => ['Custom greater than max message.']],
            ],
            'custom greater than max message with parameters' => [
                'abcd',
                [
                    new HasLength(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['' => ['Max - 3, attribute - , number - 4.']],
            ],
            'custom greater than max message with parameters, attribute set' => [
                ['data' => 'abcd'],
                [
                    'data' => new HasLength(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['data' => ['Max - 3, attribute - data, number - 4.']],
            ],

            'custom not exactly message' => [
                'abcd',
                [new HasLength(exactly: 3, notExactlyMessage: 'Custom not exactly message.')],
                ['' => ['Custom not exactly message.']],
            ],
            'custom not exactly message with parameters' => [
                'abcd',
                [
                    new HasLength(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['' => ['Exactly - 3, attribute - , number - 4.']],
            ],
            'custom not exactly message with parameters, attribute set' => [
                ['data' => 'abcd'],
                [
                    'data' => new HasLength(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['data' => ['Exactly - 3, attribute - data, number - 4.']],
            ],
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
