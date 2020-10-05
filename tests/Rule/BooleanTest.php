<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\AbstractRule;
use Yiisoft\Validator\Rule\Boolean;

/**
 * @group validators
 */
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

            [(new Boolean())->strict(true), '0', true],
            [(new Boolean())->strict(true), '1', true],

            [(new Boolean())->strict(true), true, false],
            [(new Boolean())->strict(true), false, false],

            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), '0', false],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), [], false],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), true, true],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), false, true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(AbstractRule $rule, $value, bool $expected): void
    {
        $this->assertSame($expected, $rule->validate($value)->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('boolean', (new Boolean())->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                (new Boolean()),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ]
            ],
            [
                (new Boolean())->skipOnEmpty(true),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ]
            ],
            [
                (new Boolean())->skipOnEmpty(true)->skipOnError(false),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ]
            ],
            [
                (new Boolean())->skipOnEmpty(true)->skipOnError(false)->strict(true),
                [
                    'strict' => true,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ]
            ],
            [
                (new Boolean())->trueValue('YES'),
                [
                    'strict' => false,
                    'trueValue' => 'YES',
                    'falseValue' => '0',
                    'message' => 'The value must be either "YES" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ]
            ],
            [
                (new Boolean())->falseValue('NO'),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => 'NO',
                    'message' => 'The value must be either "1" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ]
            ],
            [
                (new Boolean())->trueValue('YES')->falseValue('NO')->strict(true),
                [
                    'strict' => true,
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'message' => 'The value must be either "YES" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ]
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     * @param AbstractRule $rule
     * @param array $expected
     */
    public function testOptions(AbstractRule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
