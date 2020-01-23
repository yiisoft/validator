<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\InRange;

/**
 * @group validators
 */
class InRangeTest extends TestCase
{
    public function testInitException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The "range" property must be set.');
        new InRange('not an array');
    }

    public function testvalidate(): void
    {
        $val = new InRange(range(1, 10, 1));
        $this->assertTrue($val->validate(1)->isValid());
        $this->assertFalse($val->validate(0)->isValid());
        $this->assertFalse($val->validate(11)->isValid());
        $this->assertFalse($val->validate(5.5)->isValid());
        $this->assertTrue($val->validate(10)->isValid());
        $this->assertTrue($val->validate('10')->isValid());
        $this->assertTrue($val->validate('5')->isValid());
    }

    public function testvalidateEmpty(): void
    {
        $rule = (new InRange(range(10, 20, 1)))->skipOnEmpty(false);
        $this->assertFalse($rule->validate(null)->isValid()); //row RangeValidatorTest.php:101
        $this->assertFalse($rule->validate('0')->isValid());
        $this->assertFalse($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate('')->isValid());

        $rule = (new InRange(range(10, 20, 1)))
            ->skipOnEmpty(false)
            ->allowArray(true);

        $this->assertTrue($rule->validate([])->isValid());
    }

    public function testValidateArrayValue(): void
    {
        $rule = (new InRange(range(1, 10, 1)))
            ->allowArray(true);

        $this->assertTrue($rule->validate([1, 2, 3, 4, 5])->isValid());
        $this->assertTrue($rule->validate([6, 7, 8, 9, 10])->isValid());
        $this->assertFalse($rule->validate([0, 1, 2])->isValid());
        $this->assertFalse($rule->validate([10, 11, 12])->isValid());
        $this->assertTrue($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testvalidateStrict(): void
    {
        $rule = (new InRange(range(1, 10, 1)))
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
        $rule = (new InRange(range(1, 10, 1)))
            ->strict()
            ->allowArray(true);

        $this->assertFalse($rule->validate(['1', '2', '3', '4', '5', '6'])->isValid());
        $this->assertFalse($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testvalidateNot()
    {
        $rule = (new InRange(range(1, 10, 1)))
            ->not();

        $this->assertFalse($rule->validate(1)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(11)->isValid());
        $this->assertTrue($rule->validate(5.5)->isValid());
        $this->assertFalse($rule->validate(10)->isValid());
        $this->assertFalse($rule->validate('10')->isValid());
        $this->assertFalse($rule->validate('5')->isValid());
    }

    public function testValidateSubsetArrayable(): void
    {
        // Test in array, values are arrays. IE: ['a'] in [['a'], ['b']]
        $rule = (new InRange([['a'], ['b']]))
            ->allowArray(false);

        $this->assertTrue($rule->validate(['a'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new InRange(['a', 'b', 'c']))
            ->allowArray(true);

        $this->assertTrue($rule->validate(['a', 'b'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new InRange(['a', 'b', 'c']))
            ->allowArray(true);

        $this->assertTrue($rule->validate(new \ArrayObject(['a', 'b']))->isValid());


        // Test range as ArrayObject.
        $rule = (new InRange(new \ArrayObject(['a', 'b'])))
            ->allowArray(false);

        $this->assertTrue($rule->validate('a')->isValid());
    }
}
