<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Boolean;

class BooleanTest extends TestCase
{
    public function validateProvider(): array
    {
        return [
            [new Boolean(), true, true],
            [new Boolean(), false, true],

            [new Boolean(), '0', true],
            [new Boolean(), '1', true],
            [new Boolean(), '5', false],

            [new Boolean(), null, false],
            [new Boolean(), [], false],

            [new Boolean(strict: true), '0', true],
            [new Boolean(strict: true), '1', true],

            [new Boolean(strict: true), true, false],
            [new Boolean(strict: true), false, false],

            [new Boolean(trueValue: true, falseValue: false, strict: true), '0', false],
            [new Boolean(trueValue: true, falseValue: false, strict: true), [], false],
            [new Boolean(trueValue: true, falseValue: false, strict: true), true, true],
            [new Boolean(trueValue: true, falseValue: false, strict: true), false, true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(Boolean $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testGetName(): void
    {
        $rule = new Boolean();
        $this->assertEquals('boolean', $rule->getName());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new Boolean(),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(strict: true, skipOnEmpty: true),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => true,
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(trueValue: 'YES'),
                [
                    'trueValue' => 'YES',
                    'falseValue' => '0',
                    'strict' => false,
                    'message' => 'The value must be either "YES" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(falseValue: 'NO'),
                [
                    'trueValue' => '1',
                    'falseValue' => 'NO',
                    'strict' => false,
                    'message' => 'The value must be either "1" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Boolean(trueValue: 'YES', falseValue: 'NO', strict: true),
                [
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'strict' => true,
                    'message' => 'The value must be either "YES" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Boolean $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
