<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Validator\DumpedRuleInterface;

trait RuleWithOptionsTestTrait
{
    abstract public static function dataOptions(): array;

    #[DataProvider('dataOptions')]
    public function testOptions(DumpedRuleInterface $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }
}
