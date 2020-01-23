<?php

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
}
