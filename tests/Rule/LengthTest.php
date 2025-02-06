<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\LengthHandler;
use Yiisoft\Validator\Tests\Rule\Base\CountableLimitTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class LengthTest extends RuleTestCase
{
    use CountableLimitTestTrait;
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Length(min: 3);
        $this->assertSame(Length::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'min and max' => [
                new Length(min: 1, max: 5),
                [
                    'min' => 1,
                    'max' => 5,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'template' => '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 1],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 5],
                    ],
                    'notExactlyMessage' => [
                        'template' => '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be a string. {type} given.',
                        'parameters' => [],
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'exactly, custom' => [
                new Length(
                    exactly: 3,
                    incorrectInputMessage: 'Custom message 1.',
                    lessThanMinMessage: 'Custom message 2.',
                    greaterThanMaxMessage: 'Custom message 3.',
                    notExactlyMessage: 'Custom message 4.',
                    encoding: 'windows-1251',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'min' => null,
                    'max' => null,
                    'exactly' => 3,
                    'lessThanMinMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => 'Custom message 3.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'template' => 'Custom message 4.',
                        'parameters' => ['exactly' => 3],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            [str_repeat('x', 25), [new Length(exactly: 25)]],
            [str_repeat('€', 25), [new Length(exactly: 25)]],

            [str_repeat('x', 125), [new Length(min: 25)]],
            [str_repeat('€', 25), [new Length(min: 25)]],

            [str_repeat('x', 25), [new Length(max: 25)]],
            [str_repeat('Ä', 24), [new Length(max: 25)]],
            ['', [new Length(max: 25)]],

            [str_repeat('x', 15), [new Length(min: 10, max: 25)]],
            [str_repeat('x', 10), [new Length(min: 10, max: 25)]],
            [str_repeat('x', 20), [new Length(min: 10, max: 25)]],
            [str_repeat('x', 25), [new Length(min: 10, max: 25)]],

            [str_repeat('x', 5), [new Length(min: 1)]],
            [str_repeat('x', 5), [new Length(max: 100)]],

            'value: empty string, exactly: 0' => [
                '',
                [new Length(0)],
            ],
            'value: empty string, min: 0' => [
                '',
                [new Length(min: 0)],
            ],
            'value: empty string, max: 0' => [
                '',
                [new Length(max: 0)],
            ],
            'value: empty string, exactly: positive, skipOnEmpty: true' => [
                '',
                [new Length(1, skipOnEmpty: true)],
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $greaterThanMaxMessage = 'Value must contain at most 25 characters.';
        $notExactlyMessage = 'Value must contain exactly 25 characters.';
        $lessThanMinMessage = 'Value must contain at least 25 characters.';

        return [
            'incorrect input, array' => [
                ['not a string'],
                [new Length(min: 25)],
                ['' => ['Value must be a string. array given.']],
            ],
            'incorrect input, boolean (true)' => [
                true,
                [new Length(min: 25)],
                ['' => ['Value must be a string. bool given.']],
            ],
            'incorrect input, boolean (false)' => [
                false,
                [new Length(min: 25)],
                ['' => ['Value must be a string. bool given.']],
            ],
            'custom incorrect input message' => [
                false,
                [new Length(min: 25, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                false,
                [new Length(min: 25, incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - bool.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['data' => false],
                ['data' => new Length(min: 25, incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['data' => ['Property - data, type - bool.']],
            ],

            [
                new SingleValueDataSet(new stdClass()),
                [new Length(min: 25)],
                ['' => ['Value must be a string. stdClass given.']],
            ],

            [str_repeat('x', 1250), [new Length(max: 25)], ['' => [$greaterThanMaxMessage]]],
            [str_repeat('x', 125), [new Length(exactly: 25)], ['' => [$notExactlyMessage]]],

            ['', [new Length(exactly: 25)], ['' => [$notExactlyMessage]]],
            ['', [new Length(25)], ['' => [$notExactlyMessage]]],
            [
                str_repeat('x', 5),
                [new Length(min: 10, max: 25)],
                ['' => ['Value must contain at least 10 characters.']],
            ],
            [str_repeat('x', 13), [new Length(min: 25)], ['' => [$lessThanMinMessage]]],
            ['', [new Length(min: 25)], ['' => [$lessThanMinMessage]]],

            'custom less than min message' => [
                'ab',
                [new Length(min: 3, lessThanMinMessage: 'Custom less than min message.')],
                ['' => ['Custom less than min message.']],
            ],
            'custom less than min message with parameters' => [
                'ab',
                [new Length(min: 3, lessThanMinMessage: 'Min - {min}, property - {Property}, number - {number}.')],
                ['' => ['Min - 3, property - Value, number - 2.']],
            ],
            'custom less than min message with parameters, property set' => [
                ['data' => 'ab'],
                [
                    'data' => new Length(
                        min: 3,
                        lessThanMinMessage: 'Min - {min}, property - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Min - 3, property - data, number - 2.']],
            ],

            'custom greater than max message' => [
                'abcd',
                [new Length(max: 3, greaterThanMaxMessage: 'Custom greater than max message.')],
                ['' => ['Custom greater than max message.']],
            ],
            'custom greater than max message with parameters' => [
                'abcd',
                [
                    new Length(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, property - {property}, number - {number}.',
                    ),
                ],
                ['' => ['Max - 3, property - value, number - 4.']],
            ],
            'custom greater than max message with parameters, property set' => [
                ['data' => 'abcd'],
                [
                    'data' => new Length(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, property - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Max - 3, property - data, number - 4.']],
            ],

            'custom not exactly message' => [
                'abcd',
                [new Length(exactly: 3, notExactlyMessage: 'Custom not exactly message.')],
                ['' => ['Custom not exactly message.']],
            ],
            'custom not exactly message with parameters' => [
                'abcd',
                [
                    new Length(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, property - {Property}, number - {number}.',
                    ),
                ],
                ['' => ['Exactly - 3, property - Value, number - 4.']],
            ],
            'custom not exactly message with parameters, property set' => [
                ['data' => 'abcd'],
                [
                    'data' => new Length(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, property - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Exactly - 3, property - data, number - 4.']],
            ],

            'value: string with greater count, exactly: 0' => [
                'a',
                [new Length(0)],
                ['' => ['Value must contain exactly 0 characters.']],
            ],
            'value: empty string, exactly: positive' => [
                '',
                [new Length(1)],
                ['' => ['Value must contain exactly 1 character.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Length(min: 3), new Length(min: 3, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Length(min: 3), new Length(min: 3, when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Length::class, LengthHandler::class];
    }

    protected function getRuleClass(): string
    {
        return Length::class;
    }
}
