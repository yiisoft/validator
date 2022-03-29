<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\InRange;

/**
 * @group validators
 */
class InRangeTest extends TestCase
{
    public function testValidate(): void
    {
        $rule = new InRange(range(1, 10));

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertFalse($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate(11)->isValid());
        $this->assertFalse($rule->validate(5.5)->isValid());
        $this->assertTrue($rule->validate(10)->isValid());
        $this->assertTrue($rule->validate('10')->isValid());
        $this->assertTrue($rule->validate('5')->isValid());
    }

    public function testValidateEmpty(): void
    {
        $rule = new InRange(range(10, 20));

        $this->assertFalse($rule->validate(null)->isValid()); // row RangeValidatorTest.php:101
        $this->assertFalse($rule->validate('0')->isValid());
        $this->assertFalse($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate('')->isValid());
    }

    public function testValidateArrayValue(): void
    {
        // Test in array, values are arrays. IE: ['a'] in [['a'], ['b']]
        $rule = new InRange([['a'], ['b']]);
        $this->assertTrue($rule->validate(['a'])->isValid());

        // Test range as ArrayObject
        $rule = new InRange(new ArrayObject(['a', 'b']));
        $this->assertTrue($rule->validate('a')->isValid());
    }

    public function testValidateStrict(): void
    {
        $rule = new InRange(range(1, 10), strict: true);

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertTrue($rule->validate(5)->isValid());
        $this->assertTrue($rule->validate(10)->isValid());
        $this->assertFalse($rule->validate('1')->isValid());
        $this->assertFalse($rule->validate('10')->isValid());
        $this->assertFalse($rule->validate('5.5')->isValid());
    }

    public function testValidateArrayValueStrict(): void
    {
        $rule = new InRange(range(1, 10), strict: true);

        $this->assertFalse($rule->validate(['1', '2', '3', '4', '5', '6'])->isValid());
        $this->assertFalse($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateNot()
    {
        $rule = new InRange(range(1, 10), not: true);

        $this->assertFalse($rule->validate(1)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(11)->isValid());
        $this->assertTrue($rule->validate(5.5)->isValid());
        $this->assertFalse($rule->validate(10)->isValid());
        $this->assertFalse($rule->validate('10')->isValid());
        $this->assertFalse($rule->validate('5')->isValid());
    }

    public function testName(): void
    {
        $rule = new InRange(range(1, 10));
        $this->assertEquals('inRange', $rule->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                new InRange(range(1, 10)),
                [
                    'range' => range(1, 10),
                    'strict' => false,
                    'not' => false,
                    'message' => 'This value is invalid.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), strict: true),
                [
                    'range' => [1, 2],
                    'strict' => true,
                    'not' => false,
                    'message' => 'This value is invalid.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), not: true),
                [
                    'range' => [1, 2],
                    'strict' => false,
                    'not' => true,
                    'message' => 'This value is invalid.',
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
