<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Lazy;
use Yiisoft\Validator\Rule\LazyHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class LazyHandlerTest extends AbstractRuleValidatorTest
{
    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new LazyHandler($this->getTranslator());
    }

    public function customErrorMessagesProvider(): array
    {
        return [];
    }

    public function passedValidationProvider(): array
    {
        return [
            'at least one succeed property' => [
                new Lazy([
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
                new Lazy([
                    new HasLength(min: 10),
                    new HasLength(max: 1),
                ]),
                'hello',
                [new Error('This value must contain at least 10 characters.')],
            ],
            'case2' => [
                new Lazy([
                    new HasLength(max: 1),
                    new HasLength(min: 10),
                ]),
                'hello',
                [new Error('This value must contain at most 1 character.')],
            ],
            'nested rules instead of plain structure' => [
                new Lazy([
                    [
                        new HasLength(max: 1),
                        new HasLength(min: 10),
                    ],
                ]),
                'hello',
                [new Error('This value must contain at most 1 character.')],
            ],
        ];
    }
}
