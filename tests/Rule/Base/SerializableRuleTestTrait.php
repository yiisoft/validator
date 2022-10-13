<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Yiisoft\Validator\SerializableRuleInterface;

trait SerializableRuleTestTrait
{
    abstract public function dataOptions(): array;

    public function beforeTestOptions(): void
    {
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(SerializableRuleInterface $rule, array $expectedOptions): void
    {
        $this->beforeTestOptions();

        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }
}
