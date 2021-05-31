<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Subset;
use PHPUnit\Framework\TestCase;

class SubsetTest extends TestCase
{
    public function testValidateArrayValue(): void
    {
        $rule = Subset::rule(range(1, 10));
        $this->assertTrue($rule->validate([1, 2, 3, 4, 5])->isValid());
        $this->assertTrue($rule->validate([6, 7, 8, 9, 10])->isValid());
        $this->assertFalse($rule->validate([0, 1, 2])->isValid());
        $this->assertFalse($rule->validate([10, 11, 12])->isValid());
        $this->assertTrue($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateSubsetArrayable(): void
    {
        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = Subset::rule(['a', 'b', 'c']);

        $this->assertTrue($rule->validate(['a', 'b'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = Subset::rule(['a', 'b', 'c']);

        $this->assertTrue($rule->validate(new \ArrayObject(['a', 'b']))->isValid());
    }

    public function testValidateEmpty()
    {
        $rule = Subset::rule(range(10, 20, 1))
            ->skipOnEmpty(false);

        $this->assertTrue($rule->validate([])->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('subset', Subset::rule(range(1, 10))->getName());
    }
}
