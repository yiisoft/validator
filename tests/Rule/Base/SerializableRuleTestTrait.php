<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Yiisoft\Validator\SerializableRuleInterface;

trait SerializableRuleTestTrait
{
    abstract public function dataOptions(): array;

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(SerializableRuleInterface $rule, array $expectedOptions): void
    {
        $this->beforeTestOptions();

        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    protected function beforeTestOptions(): void
    {
    }
}
