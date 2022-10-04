<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\ValidationContext;

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

    public function testDifferentRule(): void
    {
        $this->expectException(UnexpectedRuleException::class);
        $this->validate('value', new stdClass());
    }

    protected function validate(mixed $value, object $config): Result
    {
        $ruleHandler = $this->getRuleHandler();
        $context = $this->getValidationContext();

        return $ruleHandler->validate($value, $config, $context);
    }

    abstract public function customErrorMessagesProvider(): array;

    abstract public function passedValidationProvider(): array;

    abstract public function failedValidationProvider(): array;

    abstract protected function getRuleHandler(): RuleHandlerInterface;

    protected function getValidationContext(): ValidationContext
    {
        $validator = FakeValidatorFactory::make();

        return new ValidationContext(
            $validator,
            new ArrayDataSet(['attribute' => 100, 'number' => 100, 'string' => '100']),
            'number'
        );
    }

    /**
     * @param mixed $value
     * @param Error[] $errors
     *
     * @return string[]
     */
    protected function createValueAndErrorsPair(mixed $value, array $errors): array
    {
        $newErrors = [];

        foreach ($errors as $error) {
            $newErrors[] = new Error(
                (string) $error->getMessage(),
                $error->getValuePath(),
                array_merge($error->getParameters(), ['value' => $value])
            );
        }

        return [$value, $newErrors];
    }
}
