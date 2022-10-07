<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Tests\Stub\TranslatorFactory;

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
        $translator = (new TranslatorFactory())->create();
        $resolver = new SimpleRuleHandlerContainer($translator);
        $rule = $this->getRule();
        $this->assertInstanceOf($rule->getHandlerClassName(), $resolver->resolve($rule->getHandlerClassName()));
    }

    abstract public function optionsDataProvider(): array;

    /**
     * @return SerializableRuleInterface
     */
    abstract protected function getRule(): SerializableRuleInterface;
}
