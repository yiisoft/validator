<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\HasLength;

class HasLengthTest extends TestCase
{
    public function testMin(): void
    {
        $rule1 = new HasLength(min: 1);
        $this->assertSame(1, $rule1->getOptions()['min']);

        $rule2 = $rule1->min(2);
        $this->assertSame(2, $rule2->getOptions()['min']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMax(): void
    {
        $rule1 = new HasLength(max: 1);
        $this->assertSame(1, $rule1->getOptions()['max']);

        $rule2 = $rule1->max(2);
        $this->assertSame(2, $rule2->getOptions()['max']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMessage(): void
    {
        $rule1 = new HasLength(message: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['message']);

        $rule2 = $rule1->message('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['message']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testTooShortMessage(): void
    {
        $rule1 = new HasLength(tooShortMessage: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['tooShortMessage']);

        $rule2 = $rule1->tooShortMessage('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['tooShortMessage']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testTooLongMessage(): void
    {
        $rule1 = new HasLength(tooLongMessage: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['tooLongMessage']);

        $rule2 = $rule1->tooLongMessage('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['tooLongMessage']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testEncoding(): void
    {
        $rule1 = new HasLength(encoding: 'Encoding 1.');
        $this->assertSame('Encoding 1.', $rule1->getOptions()['encoding']);

        $rule2 = $rule1->encoding('Encoding 2.');
        $this->assertSame('Encoding 2.', $rule2->getOptions()['encoding']);

        $this->assertNotSame($rule1, $rule2);
    }

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

    public function testTooShortMessageInErrorMessages(): void
    {
        $rule = new HasLength(min: 1);
        $result = $rule->validate('');

        $this->assertEquals(
            ['This value should contain at least {min, number} {min, plural, one{character} other{characters}}.'],
            $result->getErrorMessages()
        );
    }

    public function testTooLongMessageInErrorMessages(): void
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

    public function testGetName(): void
    {
        $rule = new HasLength();
        $this->assertEquals('hasLength', $rule->getName());
    }

    public function getOptionsProvider(): array
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
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Rule $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
