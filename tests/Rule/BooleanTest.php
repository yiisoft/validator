<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Boolean;

/**
 * @group validators
 */
class BooleanTest extends TestCase
{
    public function validateProvider(): array
    {
        return [
            [Boolean::rule(), true, true],
            [Boolean::rule(), false, true],

            [Boolean::rule(), '0', true],
            [Boolean::rule(), '1', true],
            [Boolean::rule(), '5', false],

            [Boolean::rule(), null, false],
            [Boolean::rule(), [], false],

            [Boolean::rule()->strict(true), '0', true],
            [Boolean::rule()->strict(true), '1', true],

            [Boolean::rule()->strict(true), true, false],
            [Boolean::rule()->strict(true), false, false],

            [Boolean::rule()->strict(true)->trueValue(true)->falseValue(false), '0', false],
            [Boolean::rule()->strict(true)->trueValue(true)->falseValue(false), [], false],
            [Boolean::rule()->strict(true)->trueValue(true)->falseValue(false), true, true],
            [Boolean::rule()->strict(true)->trueValue(true)->falseValue(false), false, true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(Rule $rule, $value, bool $expected): void
    {
        $this->assertSame($expected, $rule->validate($value)->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('boolean', Boolean::rule()->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                Boolean::rule(),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->skipOnEmpty(true),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->skipOnEmpty(true),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->skipOnEmpty(true)->strict(true),
                [
                    'strict' => true,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'message' => 'The value must be either "1" or "0".',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->trueValue('YES'),
                [
                    'strict' => false,
                    'trueValue' => 'YES',
                    'falseValue' => '0',
                    'message' => 'The value must be either "YES" or "0".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->falseValue('NO'),
                [
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => 'NO',
                    'message' => 'The value must be either "1" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                Boolean::rule()->trueValue('YES')->falseValue('NO')->strict(true),
                [
                    'strict' => true,
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'message' => 'The value must be either "YES" or "NO".',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
