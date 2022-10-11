<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\HasLength;

final class HasLengthTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new HasLength(min: 3);
        $this->assertSame('hasLength', $rule->getName());
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(HasLength $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new HasLength(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'exactly' => null,
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
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
                    'exactly' => null,
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 3],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
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
                    'exactly' => null,
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 4],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function initWithMinAndMaxAndExactlyDataProvider(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider initWithMinAndMaxAndExactlyDataProvider
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');

        new HasLength(...$arguments);
    }

    public function testInitWithMinAndMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');

        new HasLength(min: 3, max: 3);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');

        new HasLength();
    }
}
