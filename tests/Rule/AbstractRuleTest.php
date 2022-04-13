<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ParametrizedRuleInterface;

abstract class AbstractRuleTest extends TestCase
{
    /**
     * @dataProvider optionsDataProvider
     */
    public function testOptions(ParametrizedRuleInterface $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }

//    public function testGetName(): void
//    {
//        $rule = $this->getRule();
//        $this->assertEquals('atLeast', $rule->getName());
//    }

    abstract protected function optionsDataProvider(): array;

    abstract protected function getRule(): ParametrizedRuleInterface;
}
