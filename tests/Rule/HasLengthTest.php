<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\HasLength;

class HasLengthTest extends TestCase
{
    public function validateProvider(): array
    {
        return [
            [new HasLength(), ['not a string'], false],
            [new HasLength(), new \stdClass(), false],
            [new HasLength(), 'Just some string', true],
            [new HasLength(), true, false],
            [new HasLength(), false, false],

            [new HasLength(min: 25, max: 25), str_repeat('x', 25), true],
            [new HasLength(min: 25, max: 25), str_repeat('€', 25), true],
            [new HasLength(min: 25, max: 25), str_repeat('x', 125), false],
            [new HasLength(min: 25, max: 25), '', false],

            [new HasLength(min: 25), str_repeat('x', 125), true],
            [new HasLength(min: 25), str_repeat('€', 25), true],
            [new HasLength(min: 25), str_repeat('x', 13), false],
            [new HasLength(min: 25), '', false],

            [new HasLength(max: 25), str_repeat('x', 25), true],
            [new HasLength(max: 25), str_repeat('Ä', 24), true],
            [new HasLength(max: 25), str_repeat('x', 1250), false],
            [new HasLength(max: 25), '', true],

            [new HasLength(min: 10, max: 25), str_repeat('x', 15), true],
            [new HasLength(min: 10, max: 25), str_repeat('x', 10), true],
            [new HasLength(min: 10, max: 25), str_repeat('x', 20), true],
            [new HasLength(min: 10, max: 25), str_repeat('x', 25), true],
            [new HasLength(min: 10, max: 25), str_repeat('x', 5), false],
            [new HasLength(min: 10, max: 25), '', false],

            [new HasLength(min: 1), str_repeat('x', 5), true],
            [new HasLength(max: 100), str_repeat('x', 5), true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(HasLength $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testTooShortMessage(): void
    {
        $rule = new HasLength(min: 1);
        $result = $rule->validate('');

        $this->assertEquals(
            ['This value should contain at least {min, number} {min, plural, one{character} other{characters}}.'],
            $result->getErrorMessages()
        );
    }

    public function testTooLongMessage(): void
    {
        $rule = new HasLength(max: 100);
        $result = $rule->validate(str_repeat('x', 1230));

        $this->assertEquals(
            ['This value should contain at most {max, number} {max, plural, one{character} other{characters}}.'],
            $result->getErrorMessages()
        );
    }

    public function customErrorMessagesProvider(): array
    {
        $rule = new HasLength(
            min: 3,
            max: 5,
            message: 'is not string error',
            tooShortMessage: 'is too short test',
            tooLongMessage: 'is too long test'
        );

        return [
            [$rule, null, ['is not string error']],
            [$rule, str_repeat('x', 1), ['is too short test']],
            [$rule, str_repeat('x', 6), ['is too long test']],
        ];
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider customErrorMessagesProvider
     */
    public function testCustomErrorMessages(HasLength $rule, mixed $value, array $expectedErrorMessages): void
    {
        $result = $rule->validate($value);
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());
    }

    public function testName(): void
    {
        $rule = new HasLength();
        $this->assertEquals('hasLength', $rule->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                new HasLength(),
                [
                    'min' => null,
                    'max' => null,
                    'message' => 'This value must be a string.',
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'message' => 'This value must be a string.',
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
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
                    'message' => 'This value must be a string.',
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
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
                    'message' => 'This value must be a string.',
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     */
    public function testOptions(Rule $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
