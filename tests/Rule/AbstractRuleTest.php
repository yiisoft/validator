<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNever;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;

abstract class AbstractRuleTest extends TestCase
{
    /**
     * @dataProvider optionsDataProvider
     */
    public function testOptions(SerializableRuleInterface $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }

    public function testGetName(): void
    {
        $rule = $this->getRule();
        $this->assertEquals(lcfirst(substr($rule::class, strrpos($rule::class, '\\') + 1)), $rule->getName());
    }

    public function testHandlerClassName(): void
    {
        $resolver = new SimpleRuleHandlerContainer();
        $rule = $this->getRule();
        $this->assertInstanceOf($rule->getHandlerClassName(), $resolver->resolve($rule->getHandlerClassName()));
    }

    public function testInitSkipOnEmpty(): void
    {
        $rule = $this->getRule();
        $this->assertFalse($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipNever::class, $rule->getSkipOnEmptyCallback());

        $rule = $this->getRule()->skipOnEmpty(true);
        $this->assertTrue($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipOnEmpty::class, $rule->getSkipOnEmptyCallback());

        $rule = $this->getRule()->skipOnEmpty(false);
        $this->assertFalse($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipNever::class, $rule->getSkipOnEmptyCallback());

        $rule = $this->getRule()->skipOnEmptyCallback(new SkipOnNull());
        $this->assertTrue($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipOnNull::class, $rule->getSkipOnEmptyCallback());
    }

    abstract protected function optionsDataProvider(): array;

    /**
     * @return BeforeValidationTrait|SerializableRuleInterface
     */
    abstract protected function getRule(): SerializableRuleInterface;
}
