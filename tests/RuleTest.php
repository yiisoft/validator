<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\InRange;

class RuleTest extends TestCase
{
    public function namesProvider(): array
    {
        return [
            [new Boolean(), 'boolean'],
            [new InRange(range(1, 10)), 'inrange'],
        ];
    }

    public function optionsProvider(): array
    {
        return [
            [(new Boolean()), []],
            [(new Boolean())->skipOnEmpty(true), ['skipOnEmpty' => true]],
            [(new Boolean())->skipOnEmpty(true)->skipOnError(false), ['skipOnEmpty' => true, 'skipOnError' => false]],
            [(new Boolean())->skipOnEmpty(true)->skipOnError(false)->strict(true), ['skipOnEmpty' => true, 'skipOnError' => false, 'strict' => true]],
            [(new Boolean())->trueValue('YES'), ['trueValue' => 'YES']],
            [(new Boolean())->falseValue('NO'), ['falseValue' => 'NO']],
            [(new Boolean())->trueValue('YES')->falseValue('NO')->strict(true), ['strict' => true, 'trueValue' => 'YES', 'falseValue' => 'NO']],
        ];
    }

    /**
     * @dataProvider namesProvider
     * @throws \ReflectionException
     */
    public function testName(Rule $rule, string $expectedName): void
    {
        $this->assertEquals($expectedName, $rule->getName());
    }

    /**
     * @dataProvider optionsProvider
     */
    public function testDefaultOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }

}
