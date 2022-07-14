<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\EqualHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class EqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageEqual = 'Value must be equal to "{targetValueOrAttribute}".';

        return [
            [
                new Equal($value),
                101,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new Equal($value, strict: true),
                $value + 1,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new Equal(targetAttribute: 'attribute', strict: true),
                $value + 1,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => 'attribute']))],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new Equal($value), $value],
            [new Equal($value), (string)$value],
            [new Equal(targetAttribute: 'attribute', strict: true), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Equal(100, message: 'Custom error'),
                101,
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new EqualHandler();
    }
}
