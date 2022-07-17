<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class CompositeHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return [
            [
                new Composite(
                    rules: [new Number(max: 13), new Number(min: 21)],
                    when: fn () => true,
                ),
                20,
                [
                    new Error($this->formatMessage('Value must be no greater than {max}.', ['max' => 13])),
                    new Error($this->formatMessage('Value must be no less than {min}.', ['min' => 21])),
                ],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [
                new Composite(
                    rules: [new Number(max: 13)],
                    when: fn () => false,
                ),
                20,
            ],
            [
                new Composite(
                    rules: [new Number(max: 13)],
                    skipOnError: true,
                ),
                20,
            ],
            [
                new Composite(
                    rules: [new Number(max: 13)],
                    skipOnEmpty: true,
                ),
                null,
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Composite(
                    rules: [new Number(max: 13, tooBigMessage: 'Custom error')],
                    when: fn () => true,
                ),
                20,
                [
                    new Error('Custom error'),
                ],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompositeHandler();
    }
}
