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
        $val = new HasLength();
        $this->assertFalse($val->validate(['not a string'])->isValid());
        $this->assertFalse($val->validate(new \stdClass())->isValid());
        $this->assertTrue($val->validate('Just some string')->isValid());
        $this->assertFalse($val->validate(true)->isValid());
        $this->assertFalse($val->validate(false)->isValid());
    }

    public function testValidateLength(): void
    {
        $val = (new HasLength())
            ->min(25)
            ->max(25);
        $this->assertTrue($val->validate(str_repeat('x', 25))->isValid());
        $this->assertTrue($val->validate(str_repeat('€', 25))->isValid());
        $this->assertFalse($val->validate(str_repeat('x', 125))->isValid());
        $this->assertFalse($val->validate('')->isValid());

        $val = (new HasLength())
            ->min(25);
        $this->assertTrue($val->validate(str_repeat('x', 125))->isValid());
        $this->assertTrue($val->validate(str_repeat('€', 25))->isValid());
        $this->assertFalse($val->validate(str_repeat('x', 13))->isValid());
        $this->assertFalse($val->validate('')->isValid());

        $val = (new HasLength())
            ->max(25);
        $this->assertTrue($val->validate(str_repeat('x', 25))->isValid());
        $this->assertTrue($val->validate(str_repeat('Ä', 24))->isValid());
        $this->assertfalse($val->validate(str_repeat('x', 1250))->isValid());
        $this->assertTrue($val->validate('')->isValid());

        $val = (new HasLength())
            ->min(10)
            ->max(25);
        $this->assertTrue($val->validate(str_repeat('x', 15))->isValid());
        $this->assertTrue($val->validate(str_repeat('x', 10))->isValid());
        $this->assertTrue($val->validate(str_repeat('x', 20))->isValid());
        $this->assertTrue($val->validate(str_repeat('x', 25))->isValid());
        $this->assertFalse($val->validate(str_repeat('x', 5))->isValid());
        $this->assertFalse($val->validate('')->isValid());
    }

    public function testValidateMin(): void
    {
        $rule = (new HasLength())
            ->min(1);

        $result = $rule->validate('');
        $this->assertFalse($result->isValid());

        $this->assertStringContainsString(
            'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
            $result->getErrors()[0]->getMessage()
        );
        $this->assertTrue($rule->validate(str_repeat('x', 5))->isValid());
    }

    public function testValidateMax(): void
    {
        $rule = (new HasLength())
            ->max(100);

        $this->assertTrue($rule->validate(str_repeat('x', 5))->isValid());

        $result = $rule->validate(str_repeat('x', 1230));
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString(
            'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
            $result->getErrors()[0]->getMessage()
        );
    }

    public function testValidateMessages()
    {
        $rule = (new HasLength())
            ->message('is not string error')
            ->tooShortMessage('is to short test')
            ->tooLongMessage('is to long test')
            ->min(3)
            ->max(5);

        $this->assertEquals('is not string error', $rule->validate(null)->getErrors()[0]);
        $this->assertEquals('is to short test', $rule->validate(str_repeat('x', 1))->getErrors()[0]);
        $this->assertEquals('is to long test', $rule->validate(str_repeat('x', 6))->getErrors()[0]);
    }

    public function testName(): void
    {
        $this->assertEquals('hasLength', (new HasLength())->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                (new HasLength()),
                [
                    'message' => 'This value must be a string.',
                    'min' => null,
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'max' => null,
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new HasLength())->min(3),
                [
                    'message' => 'This value must be a string.',
                    'min' => 3,
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'max' => null,
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new HasLength())->max(3),
                [
                    'message' => 'This value must be a string.',
                    'min' => null,
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'max' => 3,
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new HasLength())->min(3)->max(4)->encoding('windows-1251'),
                [
                    'message' => 'This value must be a string.',
                    'min' => 3,
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'max' => 4,
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param RuleTest $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
