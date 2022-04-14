<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

abstract class AbstractRuleValidatorTest extends TestCase
{
    /**
     * @dataProvider passedValidationProvider
     */
    public function testValidationPassed(object $config, mixed $value): void
    {
        $result = $this->validate($value, $config);

        $this->assertTrue($result->isValid(), print_r($result->getErrors(), true));
    }

    /**
     * @dataProvider failedValidationProvider
     */
    public function testValidationFailed(object $config, mixed $value, array $expectedErrors): void
    {
        $result = $this->validate($value, $config);

        $this->assertFalse($result->isValid(), print_r($result->getErrors(), true));
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider customErrorMessagesProvider
     */
    public function testCustomErrorMessages(object $config, mixed $value, array $expectedErrorMessages): void
    {
        $result = $this->validate($value, $config);

        $errors = $result->getErrors();

        $this->assertFalse($result->isValid(), print_r($result->getErrors(), true));
        $this->assertEquals($expectedErrorMessages, $errors);
    }

    public function testGetName(): void
    {
        $rule = $this->getValidator();

        $this->assertEquals($this->getConfigClassName(), $rule::getConfigClassName());
    }

    protected function validate(mixed $value, object $config, ValidationContext $context = null): Result
    {
        $ruleValidator = $this->getValidator();
        $validator = new Validator();

        return $ruleValidator->validate($value, $config, $validator, $context);
    }

    abstract public function customErrorMessagesProvider(): array;

    abstract public function passedValidationProvider(): array;

    abstract public function failedValidationProvider(): array;

    abstract protected function getValidator(): RuleValidatorInterface;

    abstract protected function getConfigClassName(): string;
}
