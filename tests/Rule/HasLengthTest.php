<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\HasLength;

/**
 * @group validators
 */
class HasLengthTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new HasLength();
        $this->assertFalse($rule->validate(['not a string'])->isValid());
        $this->assertFalse($rule->validate(new \stdClass())->isValid());
        $this->assertTrue($rule->validate('Just some string')->isValid());
        $this->assertFalse($rule->validate(true)->isValid());
        $this->assertFalse($rule->validate(false)->isValid());
    }

    public function testValidateLength(): void
    {
        $rule = new HasLength(min: 25, max: 25);
        $this->assertTrue($rule->validate(str_repeat('x', 25))->isValid());
        $this->assertTrue($rule->validate(str_repeat('€', 25))->isValid());
        $this->assertFalse($rule->validate(str_repeat('x', 125))->isValid());
        $this->assertFalse($rule->validate('')->isValid());

        $rule = new HasLength(min: 25);
        $this->assertTrue($rule->validate(str_repeat('x', 125))->isValid());
        $this->assertTrue($rule->validate(str_repeat('€', 25))->isValid());
        $this->assertFalse($rule->validate(str_repeat('x', 13))->isValid());
        $this->assertFalse($rule->validate('')->isValid());

        $rule = new HasLength(max: 25);
        $this->assertTrue($rule->validate(str_repeat('x', 25))->isValid());
        $this->assertTrue($rule->validate(str_repeat('Ä', 24))->isValid());
        $this->assertfalse($rule->validate(str_repeat('x', 1250))->isValid());
        $this->assertTrue($rule->validate('')->isValid());

        $rule = new HasLength(min: 10, max: 25);
        $this->assertTrue($rule->validate(str_repeat('x', 15))->isValid());
        $this->assertTrue($rule->validate(str_repeat('x', 10))->isValid());
        $this->assertTrue($rule->validate(str_repeat('x', 20))->isValid());
        $this->assertTrue($rule->validate(str_repeat('x', 25))->isValid());
        $this->assertFalse($rule->validate(str_repeat('x', 5))->isValid());
        $this->assertFalse($rule->validate('')->isValid());
    }

    public function testValidateMin(): void
    {
        $rule = new HasLength(min: 1);
        $result = $rule->validate('');

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            ['This value should contain at least {min, number} {min, plural, one{character} other{characters}}.'],
            $result->getErrorMessages()
        );
        $this->assertTrue($rule->validate(str_repeat('x', 5))->isValid());
    }

    public function testValidateMax(): void
    {
        $rule = new HasLength(max: 100);
        $this->assertTrue($rule->validate(str_repeat('x', 5))->isValid());

        $result = $rule->validate(str_repeat('x', 1230));
        $this->assertFalse($result->isValid());
        $this->assertEquals(
            ['This value should contain at most {max, number} {max, plural, one{character} other{characters}}.'],
            $result->getErrorMessages()
        );
    }

    public function testValidateMessages()
    {
        $rule = new HasLength(
            min: 3,
            max: 5,
            message: 'is not string error',
            tooShortMessage: 'is too short test',
            tooLongMessage: 'is too long test'
        );

        $this->assertEquals(['is not string error'], $rule->validate(null)->getErrorMessages());
        $this->assertEquals(['is too short test'], $rule->validate(str_repeat('x', 1))->getErrorMessages());
        $this->assertEquals(['is too long test'], $rule->validate(str_repeat('x', 6))->getErrorMessages());
    }

    public function testName(): void
    {
        $this->assertEquals('hasLength', (new HasLength())->getName());
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
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
