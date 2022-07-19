<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\RuleHandlerInterface;

final class NotEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageNotEqual = 'Value must not be equal to "{targetValueOrAttribute}".';

        return [
            [
                new NotEqual($value),
                100,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new NotEqual($value, strict: true),
                100,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new NotEqual($value), $value + 1],
            [new NotEqual($value, strict: true), '101'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new NotEqual(100, message: 'Custom error'),
                100,
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler($this->getTranslator());
    }
}
