<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\SelfValidatable;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\SelfValidatableRuleInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\ValidationContext;

abstract class AbstractSelfValidatableRuleTest extends TestCase
{
    /**
     * @dataProvider passedValidationProvider
     */
    public function testValidationPassed(SelfValidatableRuleInterface $rule, mixed $value): void
    {
        $result = $this->validate($rule, $value);

        $this->assertTrue($result->isValid(), print_r($result->getErrors(), true));
    }

    /**
     * @dataProvider failedValidationProvider
     */
    public function testValidationFailed(SelfValidatableRuleInterface $rule, mixed $value, array $expectedErrors): void
    {
        $result = $this->validate($rule, $value);

        $this->assertFalse($result->isValid(), print_r($result->getErrors(), true));
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider customErrorMessagesProvider
     */
    public function testCustomErrorMessages(SelfValidatableRuleInterface $rule, mixed $value, array $expectedErrorMessages): void
    {
        $result = $this->validate($rule, $value);

        $errors = $result->getErrors();

        $this->assertFalse($result->isValid(), print_r($result->getErrors(), true));
        $this->assertEquals($expectedErrorMessages, $errors);
    }

    protected function validate(SelfValidatableRuleInterface $rule, mixed $value): Result
    {
        $context = new ValidationContext(FakeValidatorFactory::make(), null);

        return $rule->validate($value, $context);
    }

    abstract public function customErrorMessagesProvider(): array;

    abstract public function passedValidationProvider(): array;

    abstract public function failedValidationProvider(): array;

    /**
     * @dataProvider optionsDataProvider
     */
    public function testOptions(SelfValidatableRuleInterface $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }

    public function testGetName(): void
    {
        $rule = $this->getRule();
        $this->assertEquals(lcfirst(substr($rule::class, strrpos($rule::class, '\\') + 1)), $rule->getName());
    }

    abstract protected function optionsDataProvider(): array;

    abstract protected function getRule(): SelfValidatableRuleInterface;
}
