<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Each;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Each\Each;
use Yiisoft\Validator\Rule\Each\EachValidator;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class EachValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13)]),
                [10, 20, 30],
                [
                    new Error('Value must be no greater than {max}.', ['max' => 13]),
                    new Error('Value must be no greater than {max}.', ['max' => 13]),
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
                new Each([new Number(max: 13, tooBigMessage: 'Custom error')]),
                [10, 20, 30],
                [
                    new Error('Custom error', ['max' => 13]),
                    new Error('Custom error', ['max' => 13]),
                ],
            ],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new EachValidator();
    }

    protected function getConfigClassName(): string
    {
        return Each::class;
    }
}
