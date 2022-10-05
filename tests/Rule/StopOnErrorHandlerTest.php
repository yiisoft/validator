<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class StopOnErrorHandlerTest extends AbstractRuleValidatorTest
{
    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new StopOnErrorHandler();
    }

    public function customErrorMessagesProvider(): array
    {
        return [];
    }

    public function passedValidationProvider(): array
    {
        return [
            'at least one succeed property' => [
                new StopOnError([
                    new HasLength(min: 1),
                    new HasLength(max: 10),
                ]),
                'hello',
            ],
        ];
    }

    public function failedValidationProvider(): array
    {
        return [
            'case1' => [
                new StopOnError([
                    new HasLength(min: 10),
                    new HasLength(max: 1),
                ]),
                ...$this->createValueAndErrorsPair(
                    'hello',
                    [new Error('This value must contain at least 10 characters.', parameters: ['min' => 10])]
                ),
            ],
            'case2' => [
                new StopOnError([
                    new HasLength(max: 1),
                    new HasLength(min: 10),
                ]),
                ...$this->createValueAndErrorsPair(
                    'hello',
                    [new Error('This value must contain at most 1 character.', parameters: ['max' => 1])]
                ),
            ],
            'nested rules instead of plain structure' => [
                new StopOnError([
                    [
                        new HasLength(max: 1),
                        new HasLength(min: 10),
                    ],
                ]),
                ...$this->createValueAndErrorsPair(
                    'hello',
                    [new Error('This value must contain at most 1 character.', parameters: ['max' => 1])]
                ),
            ],
        ];
    }
}
