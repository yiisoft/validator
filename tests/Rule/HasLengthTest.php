<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\HasLength;

final class HasLengthTest extends TestCase
{
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
                new HasLength(),
                [
                    'min' => null,
                    'max' => null,
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'tooShortMessage' => [
                        'message' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => null],
                    ],
                    'tooLongMessage' => [
                        'message' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => null],
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'tooShortMessage' => [
                        'message' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'tooLongMessage' => [
                        'message' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => null],
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
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'tooShortMessage' => [
                        'message' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => null],
                    ],
                    'tooLongMessage' => [
                        'message' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 3],
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
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'tooShortMessage' => [
                        'message' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'tooLongMessage' => [
                        'message' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 4],
                    ],
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }
}
