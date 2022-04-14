<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\InRange;

class InRangeTest extends TestCase
{
    public function testRange(): void
    {
        $rule1 = new InRange([1, 2]);
        $this->assertSame([1, 2], $rule1->getOptions()['range']);

        $rule2 = $rule1->range([1, 2, 3]);
        $this->assertSame([1, 2, 3], $rule2->getOptions()['range']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testStrict(): void
    {
        $rule1 = new InRange([1, 2], strict: true);
        $this->assertTrue($rule1->getOptions()['strict']);

        $rule2 = $rule1->strict(false);
        $this->assertFalse($rule2->getOptions()['strict']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testNot(): void
    {
        $rule1 = new InRange([1, 2], not: true);
        $this->assertTrue($rule1->getOptions()['not']);

        $rule2 = $rule1->not(false);
        $this->assertFalse($rule2->getOptions()['not']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMessage(): void
    {
        $rule1 = new InRange([1, 2], message: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['message']);

        $rule2 = $rule1->message('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['message']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function validateWithDefaultArgumentsProvider(): array
    {
        $range = range(1, 10);

        return [
            [$range, 1, true],
            [$range, 0, false],
            [$range, 11, false],
            [$range, 5.5, false],
            [$range, 10, true],
            [$range, '10', true],
            [$range, '5', true],
        ];
    }

    /**
     * @dataProvider validateWithDefaultArgumentsProvider
     */
    public function testValidateWithDefaultArguments(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateEmptyDataProvider(): array
    {
        $range = range(10, 20);

        return [
            [$range, null, false],
            [$range, '0', false],
            [$range, 0, false],
            [$range, '', false],
        ];
    }

    /**
     * @dataProvider validateEmptyDataProvider
     */
    public function testValidateEmpty(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateArrayValueDataProvider(): array
    {
        return [
            [[['a'], ['b']], ['a'], true],
            [new ArrayObject(['a', 'b']), 'a', true],
        ];
    }

    /**
     * @dataProvider validateArrayValueDataProvider
     */
    public function testValidateArrayValue(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateStrictDataProvider(): array
    {
        $range = range(1, 10);

        return [
            [$range, 1, true],
            [$range, 5, true],
            [$range, 10, true],
            [$range, '1', false],
            [$range, '10', false],
            [$range, '5.5', false],
        ];
    }

    /**
     * @dataProvider validateStrictDataProvider
     */
    public function testValidateStrict(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range, strict: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateArrayValueStrictProvider(): array
    {
        $range = range(1, 10);

        return [
            [$range, ['1', '2', '3', '4', '5', '6'], false],
            [$range, ['1', '2', '3', 4, 5, 6], false],
        ];
    }

    /**
     * @dataProvider validateArrayValueStrictProvider
     */
    public function testValidateArrayValueStrict(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range, strict: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateNotProvider(): array
    {
        $range = range(1, 10);

        return [
            [$range, 1, false],
            [$range, 0, true],
            [$range, 11, true],
            [$range, 5.5, true],
            [$range, 10, false],
            [$range, '10', false],
            [$range, '5', false],
        ];
    }

    /**
     * @dataProvider validateNotProvider
     */
    public function testValidateNot(iterable $range, mixed $value, bool $expectedIsValid): void
    {
        $rule = new InRange($range, not: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testGetName(): void
    {
        $rule = new InRange(range(1, 10));
        $this->assertEquals('inRange', $rule->getName());
    }

    public function getOptionsProvider(): array
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
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(InRange $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
