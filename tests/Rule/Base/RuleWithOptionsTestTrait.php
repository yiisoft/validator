<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Yiisoft\Validator\DumpedRuleInterface;

trait RuleWithOptionsTestTrait
{
    abstract public function dataOptions(): array;

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(DumpedRuleInterface $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }
}
