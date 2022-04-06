<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\Rule\Subset;
use PHPUnit\Framework\TestCase;

class SubsetTest extends TestCase
{
    public function validateArrayValueProvider(): array
    {
        $rule = new Subset(range(1, 10));

        return [
            [$rule, [1, 2, 3, 4, 5], true],
            [$rule, [6, 7, 8, 9, 10], true],
            [$rule, [0, 1, 2], false],
            [$rule, [10, 11, 12], false],
            [$rule, ['1', '2', '3', 4, 5, 6], true],
        ];
    }

    /**
     * @dataProvider validateArrayValueProvider
     */
    public function testValidateArrayValue(Subset $rule, array $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateSubsetArrayableProvider()
    {
        $rule = new Subset(['a', 'b', 'c']);

        return [
            [$rule, ['a', 'b']],
            [$rule, new ArrayObject(['a', 'b'])],
        ];
    }

    /**
     * @dataProvider validateSubsetArrayableProvider
     */
    public function testValidateSubsetArrayable(Subset $rule, array|ArrayObject $value): void
    {
        $result = $rule->validate($value);
        $this->assertTrue($result->isValid());
    }

    public function testValidateEmpty(): void
    {
        $rule = new Subset(range(10, 20));
        $this->assertTrue($rule->validate([])->isValid());
    }

    public function testName(): void
    {
        $rule = new Subset(range(1, 10));
        $this->assertEquals('subset', $rule->getName());
    }
}
