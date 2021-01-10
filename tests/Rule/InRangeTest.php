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
        $val = new InRange(range(1, 10));
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
        $rule = (new InRange(range(10, 20)))->skipOnEmpty(false);
        $this->assertFalse($rule->validate(null)->isValid()); //row RangeValidatorTest.php:101
        $this->assertFalse($rule->validate('0')->isValid());
        $this->assertFalse($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate('')->isValid());

        $rule = (new InRange(range(10, 20, 1)))
            ->skipOnEmpty(false);

        $this->assertTrue($rule->validate([])->isValid());
    }

    public function testValidateArrayValue(): void
    {
        $rule = (new InRange(range(1, 10)));

        $this->assertTrue($rule->validate([1, 2, 3, 4, 5])->isValid());
        $this->assertTrue($rule->validate([6, 7, 8, 9, 10])->isValid());
        $this->assertFalse($rule->validate([0, 1, 2])->isValid());
        $this->assertFalse($rule->validate([10, 11, 12])->isValid());
        $this->assertTrue($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateStrict(): void
    {
        $rule = (new InRange(range(1, 10)))
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
            ->strict();

        $this->assertFalse($rule->validate(['1', '2', '3', '4', '5', '6'])->isValid());
        $this->assertFalse($rule->validate(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateNot()
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
        $rule = (new InRange([['a'], ['b']]));

        $this->assertTrue($rule->validate(['a'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new InRange(['a', 'b', 'c']));

        $this->assertTrue($rule->validate(['a', 'b'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new InRange(['a', 'b', 'c']));

        $this->assertTrue($rule->validate(new \ArrayObject(['a', 'b']))->isValid());


        // Test range as ArrayObject.
        $rule = (new InRange(new \ArrayObject(['a', 'b'])));

        $this->assertTrue($rule->validate('a')->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('inRange', (new InRange(range(1, 10)))->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                (new InRange(range(1, 10))),
                [
                    'message' => 'This value is invalid.',
                    'range' => range(1, 10),
                    'strict' => false,
                    'not' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new InRange(range(1, 2)))->strict(),
                [
                    'message' => 'This value is invalid.',
                    'range' => [1, 2],
                    'strict' => true,
                    'not' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            [
                (new InRange(range(1, 2)))->not(),
                [
                    'message' => 'This value is invalid.',
                    'range' => [1, 2],
                    'strict' => false,
                    'not' => true,
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
