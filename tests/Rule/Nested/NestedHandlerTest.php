<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength\HasLength;
use Yiisoft\Validator\Rule\InRange\InRange;
use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\Rule\Nested\NestedHandler;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\Rule\Regex\Regex;
use Yiisoft\Validator\Rule\Required\Required;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class NestedHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $requiredRule = new Required();
        $rule = new Nested(['value' => $requiredRule]);
        $value = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        return [
            'error' => [
                new Nested(['author.age' => [new Number(min: 20)]]),
                $value,
                [new Error('Value must be no less than {min}.', ['min' => 20], 'author.age')],
            ],
            'key not exists' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'])]]),
                $value,
                [new Error('This value is invalid.', [], 'author.sex')],
            ],
            [
                $rule,
                '',
                // TODO: move message to rule
                [new Error('Value should be an array or an object. string given.', [], '')],
            ],
            [
                $rule,
                ['value' => null],
                [new Error($requiredRule->message, [], 'value')],
            ],
            [
                new Nested(['value' => new Required()], errorWhenPropertyPathIsNotFound: true),
                [],
                [new Error($rule->propertyPathIsNotFoundMessage, ['path' => 'value'], 'value')],
            ],
            [
                //                 @link https://github.com/yiisoft/validator/issues/200
                new Nested([
                    'body.shipping' => [
                        new Required(),
                        new Nested([
                            'phone' => [new Regex('/^\+\d{11}$/')],
                        ]),
                    ],
                ]),
                [
                    'body' => [
                        'shipping' => [
                            'phone' => '+777777777777',
                        ],
                    ],
                ],
                [new Error('Value is invalid.', [], 'body.shipping.phone')],
            ],
            [
                new Nested([
                    0 => new Nested([
                        0 => [new Number(min: -10, max: 10)],
                    ]),
                ]),
                [0 => [0 => -11]],
                [new Error('Value must be no less than {min}.', ['min' => -10], '0.0')],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        return [
            [
                new Nested([
                    'author.name' => [
                        new HasLength(min: 3),
                    ],
                ]),
                $value,
            ],
            [
                new Nested([
                    'author' => [
                        new Required(),
                        new Nested([
                            'name' => [new HasLength(min: 3)],
                        ]),
                    ],
                ]),
                $value,
            ],
            'key not exists, skip empty' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'], skipOnEmpty: true)]]),
                $value,
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Nested(
                    ['value' => new Required()],
                    errorWhenPropertyPathIsNotFound: true,
                    propertyPathIsNotFoundMessage: 'Property is not found.',
                ),
                [],
                [new Error('Property is not found.', ['path' => 'value'], 'value')],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new NestedHandler();
    }
}
