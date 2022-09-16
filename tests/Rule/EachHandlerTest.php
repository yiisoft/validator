<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\TranslateValidatorDecorator;
use Yiisoft\Validator\ValidationContext;

final class EachHandlerTest extends AbstractRuleValidatorTest
{
    public function indexedByPathErrorMessagesProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13)]),
                [10, 20, 30],
                [
                    '1' => ['Value must be no greater than {max}.'],
                    '2' => ['Value must be no greater than {max}.'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider indexedByPathErrorMessagesProvider
     */
    public function testErrorMessagesIndexedByPath(object $rule, $value, array $expectedErrors): void
    {
        $result = $this->validate($value, $rule);

        $this->assertFalse($result->isValid(), print_r($result->getErrorMessagesIndexedByPath(), true));
        $this->assertEquals($expectedErrors, array_map(
            fn (array $errors) => array_map(fn (ErrorMessage $error) => $error->getMessage(), $errors),
            $result->getErrorMessagesIndexedByPath()
        ));
    }

    public function failedValidationProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13)]),
                [10, 20, 30],
                [
                    new Error('Value must be no greater than {max}.', [1], ['max' => 13, 'value' => 20]),
                    new Error('Value must be no greater than {max}.', [2], ['max' => 13, 'value' => 30]),
                ],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [
                new Each([new Number(max: 20)]),
                [10, 11],
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13, tooBigMessage: 'Custom error.')]),
                [10, 20, 30],
                [
                    new Error('Custom error.', [1], ['max' => 13, 'value' => 20]),
                    new Error('Custom error.', [2], ['max' => 13, 'value' => 30]),
                ],
            ],
        ];
    }

    protected function getValidationContext(): ValidationContext
    {
        $validator = FakeValidatorFactory::make();

        return new ValidationContext(
            $validator,
            new ArrayDataSet(['attribute' => 100, 'number' => 100, 'string' => '100']),
            'number',
            [TranslateValidatorDecorator::IS_TRANSLATION_NEEDED => false]
        );
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new EachHandler();
    }
}
