<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

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
        $val = InRange::rule(range(1, 10));
        $this->assertTrue($val->validate(1)->isValid());
        $this->assertFalse($val->validate(0)->isValid());
        $this->assertFalse($val->validate(11)->isValid());
        $this->assertFalse($val->validate(5.5)->isValid());
        $this->assertTrue($val->validate(10)->isValid());
        $this->assertTrue($val->validate('10')->isValid());
        $this->assertTrue($val->validate('5')->isValid());
    }

    public function testValidateEmpty(): void
    {
        $rule = InRange::rule(range(10, 20))->skipOnEmpty(false);
        $this->assertFalse($rule->validate(null)->isValid()); //row RangeValidatorTest.php:101
        $this->assertFalse($rule->validate('0')->isValid());
        $this->assertFalse($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate('')->isValid());
    }

    public function testValidateArrayValue(): void
    {
        // Test in array, values are arrays. IE: ['a'] in [['a'], ['b']]
        $rule = InRange::rule([['a'], ['b']]);

        $this->assertTrue($rule->validate(['a'])->isValid());


        // Test range as ArrayObject.
        $rule = InRange::rule(new \ArrayObject(['a', 'b']));

        $this->assertTrue($rule->validate('a')->isValid());
    }

    public function testValidateStrict(): void
    {
        $rule = InRange::rule(range(1, 10))
            ->strict();

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertTrue($rule->validate(5)->isValid());
        $this->assertTrue($rule->validate(10)->isValid());
        $this->assertFalse($rule->validate('1')->isValid());
        $this->assertFalse($rule->validate('10')->isValid());
        $this->assertFalse($rule->validate('5.5')->isValid());
    }

    public function testValidateArrayValueStrict(): void
    {
        $rule = InRange::rule(range(1, 10, 1))
            ->strict();

        $this->assertFalse($rule->validate(['1', '2', '3', '4', '5', '6'])->isValid());
        $this->assertFalse($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateNot()
    {
        $rule = InRange::rule(range(1, 10, 1))
            ->not();

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
        $this->assertEquals('inRange', InRange::rule(range(1, 10))->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                InRange::rule(range(1, 10)),
                [
                    'message' => 'This value is invalid.',
                    'range' => range(1, 10),
                    'strict' => false,
                    'not' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                InRange::rule(range(1, 2))->strict(),
                [
                    'message' => 'This value is invalid.',
                    'range' => [1, 2],
                    'strict' => true,
                    'not' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                InRange::rule(range(1, 2))->not(),
                [
                    'message' => 'This value is invalid.',
                    'range' => [1, 2],
                    'strict' => false,
                    'not' => true,
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
