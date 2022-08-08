<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RuleHandlerInterface;

final class EachHandlerTest extends AbstractRuleValidatorTest
{
    /**
     * @dataProvider indexedByPathErrorMessagesProvider
     */
    public function testErrorMessagesIndexedByPath(object $rule, $value, array $expectedErrors): void
    {
        $result = $this->validate($value, $rule);

        $this->assertFalse($result->isValid(), print_r($result->getErrorMessagesIndexedByPath(), true));
        $this->assertEquals($expectedErrors, $result->getErrorMessagesIndexedByPath());
    }

    public function failedValidationProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13)]),
                [10, 20, 30],
                [
                    new Error(
                        $this->formatMessage(
                            'Value must be no greater than {max}. {value} given.',
                            ['max' => 13, 'value' => 20]
                        ),
                        [1]
                    ),
                    new Error(
                        $this->formatMessage(
                            'Value must be no greater than {max}. {value} given.',
                            ['max' => 13, 'value' => 30]
                        ),
                        [2]
                    ),
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
                    new Error('Custom error. 20 given.', [1]),
                    new Error('Custom error. 30 given.', [2]),
                ],
            ],
        ];
    }

    public function indexedByPathErrorMessagesProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13)]),
                [10, 20, 30],
                [
                    '1' => [
                        $this->formatMessage(
                            'Value must be no greater than {max}. {value} given.',
                            ['max' => 13, 'value' => 20]
                        ),
                    ],
                    '2' => [
                        $this->formatMessage(
                            'Value must be no greater than {max}. {value} given.',
                            ['max' => 13, 'value' => 30]
                        ),
                    ],
                ],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new EachHandler();
    }
}
