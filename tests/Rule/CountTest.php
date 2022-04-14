<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Countable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule\Count;

class CountTest extends TestCase
{
    public function testMin(): void
    {
        $rule1 = new Count(min: 1);
        $this->assertSame(1, $rule1->getOptions()['min']);

        $rule2 = $rule1->min(2);
        $this->assertSame(2, $rule2->getOptions()['min']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMax(): void
    {
        $rule1 = new Count(max: 1);
        $this->assertSame(1, $rule1->getOptions()['max']);

        $rule2 = $rule1->max(2);
        $this->assertSame(2, $rule2->getOptions()['max']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testExactly(): void
    {
        $rule1 = new Count(exactly: 1);
        $this->assertSame(1, $rule1->getOptions()['exactly']);

        $rule2 = $rule1->exactly(2);
        $this->assertSame(2, $rule2->getOptions()['exactly']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');

        new Count();
    }

    public function testValidateWithoutRequiredArguments(): void
    {
        $rule = new Count(min: 1);
        $rule = $rule->min(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');

        $rule->validate(2);
    }

    public function withMinAndMaxAndExactlyDataProvider(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider withMinAndMaxAndExactlyDataProvider
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');

        new Count(...$arguments);
    }

    /**
     * @dataProvider withMinAndMaxAndExactlyDataProvider
     */
    public function testValidateWithMinAndMaxAndExactly(array $arguments): void
    {
        $rule = new Count(min: 1);
        foreach ($arguments as $name => $value) {
            $rule = $rule->$name($value);
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');

        $rule->validate(2);
    }

    public function testInitWithMinAndMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');

        new Count(min: 3, max: 3);
    }

    public function testValidateWithMinAndMax(): void
    {
        $rule = new Count(min: 1);
        $rule = $rule->min(3)
            ->max(3);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');

        $rule->validate(2);
    }

    public function validateWrongTypesDataProvider(): array
    {
        return [
            [1],
            [1.1],
            [''],
            ['some string'],
            [new stdClass()],
            // https://www.php.net/manual/ru/class.countable.php
            [
                new class () {
                    protected int $myCount = 3;

                    public function count(): int
                    {
                        return $this->myCount;
                    }
                },
            ],
        ];
    }

    /**
     * @dataProvider validateWrongTypesDataProvider
     */
    public function testValidateWrongTypes(mixed $value): void
    {
        $rule = new Count(min: 3);
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function validateCountable(): void
    {
        $rule = new Count(min: 3);
        $value = new class () implements Countable {
            protected int $myCount = 3;

            public function count(): int
            {
                return $this->myCount;
            }
        };
        $result = $rule->validate($value);

        $this->assertTrue($result->isValid());
    }

    public function validateWithMinDataProvider(): array
    {
        $rule = new Count(min: 3);
        $errorMessages = ['This value must contain at least {min, number} {min, plural, one{item} other{items}}.'];

        return [
            [$rule, [], false, $errorMessages],
            [$rule, [0, 0], false, $errorMessages],
            [$rule, [0, 0, 0], true, []],
            [$rule, [0, 0, 0, 0], true, []],
        ];
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider validateWithMinDataProvider
     */
    public function testValidateWithMin(
        Count $rule,
        array $value,
        bool $expectedIsValid,
        array $expectedErrorMessages
    ): void {
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());
    }

    public function testValidationWithMinAndCustomMessage(): void
    {
        $rule = new Count(min: 3, tooFewItemsMessage: 'Custom message.');
        $result = $rule->validate([0, 0]);

        $this->assertEquals(['Custom message.'], $result->getErrorMessages());
    }

    public function validateWithMaxDataProvider(): array
    {
        $rule = new Count(max: 3);
        $errorMessages = ['This value must contain at most {max, number} {max, plural, one{item} other{items}}.'];

        return [
            [$rule, [], true, []],
            [$rule, [0, 0], true, []],
            [$rule, [0, 0, 0], true, []],
            [$rule, [0, 0, 0, 0], false, $errorMessages],
        ];
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider validateWithMaxDataProvider
     */
    public function testValidateWithMax(
        Count $rule,
        array $value,
        bool $expectedIsValid,
        array $expectedErrorMessages
    ): void {
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());
    }

    public function testValidationWithMaxAndCustomMessage(): void
    {
        $rule = new Count(max: 3, tooManyItemsMessage: 'Custom message.');
        $result = $rule->validate([0, 0, 0, 0]);

        $this->assertEquals(['Custom message.'], $result->getErrorMessages());
    }

    public function testValidateWithExactly(): void
    {
        $rule = new Count(exactly: 3);
        $result = $rule->validate([0, 0, 0]);

        $this->assertTrue($result->isValid());
    }

    public function testValidationWithExactlyAndCustomMessage(): void
    {
        $rule = new Count(exactly: 3, notExactlyMessage: 'Custom message.');
        $result = $rule->validate([0, 0, 0, 0]);

        $this->assertEquals(['Custom message.'], $result->getErrorMessages());
    }

    public function testGetName(): void
    {
        $rule = new Count(min: 3);
        $this->assertSame('count', $rule->getName());
    }

    public function testGetOptions(): void
    {
        $rule = new Count(min: 3);
        $expectedOptions = [
            'skipOnEmpty' => false,
            'skipOnError' => false,
            'min' => 3,
            'max' => null,
            'exactly' => null,
            'message' => 'This value must be an array or implement \Countable interface.',
            'tooFewItemsMessage' => 'This value must contain at least {min, number} ' .
                '{min, plural, one{item} other{items}}.',
            'tooManyItemsMessage' => 'This value must contain at most {max, number} ' .
                '{max, plural, one{item} other{items}}.',
            'notExactlyMessage' => 'This value must contain exactly {max, number} ' .
                '{max, plural, one{item} other{items}}.',
        ];

        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
