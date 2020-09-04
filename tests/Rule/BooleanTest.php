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
    public function testValidate(Rule $rule, $value, bool $expected): void
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
            [(new Boolean()), ['message' => 'The value must be either "1" or "0".']],
            [(new Boolean())->skipOnEmpty(true), ['skipOnEmpty' => true, 'message' => 'The value must be either "1" or "0".']],
            [(new Boolean())->skipOnEmpty(true)->skipOnError(false),
                ['skipOnEmpty' => true, 'skipOnError' => false, 'message' => 'The value must be either "1" or "0".']],
            [(new Boolean())->skipOnEmpty(true)->skipOnError(false)->strict(true),
                ['skipOnEmpty' => true, 'skipOnError' => false, 'strict' => true, 'message' => 'The value must be either "1" or "0".']],
            [(new Boolean())->trueValue('YES'), ['trueValue' => 'YES', 'message' => 'The value must be either "YES" or "0".']],
            [(new Boolean())->falseValue('NO'), ['falseValue' => 'NO', 'message' => 'The value must be either "1" or "NO".']],
            [(new Boolean())->trueValue('YES')->falseValue('NO')->strict(true),
                ['strict' => true, 'trueValue' => 'YES', 'falseValue' => 'NO', 'message' => 'The value must be either "YES" or "NO".']],
        ];
    }

    /**
     * @dataProvider optionsProvider
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
