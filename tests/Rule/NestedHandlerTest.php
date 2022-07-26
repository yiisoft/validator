<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleHandlerInterface;

final class NestedHandlerTest extends AbstractRuleValidatorTest
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
                [new Error($this->formatMessage('Value must be no less than {min}.', ['min' => 20]), ['author', 'age'])],
            ],
            'key not exists' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'])]]),
                $value,
                [new Error('This value is invalid.', ['author', 'sex'])],
            ],
            [
                $rule,
                '',
                [new Error('Value should be an array or an object. string given.', [])],
            ],
            [
                $rule,
                ['value' => null],
                [new Error($requiredRule->getMessage(), ['value'])],
            ],
            [
                new Nested(['value' => new Required()], requirePropertyPath: true),
                [],
                [new Error($this->formatMessage($rule->getNoPropertyPathMessage(), ['path' => 'value']), ['value'])],
            ],
            [
                // @link https://github.com/yiisoft/validator/issues/200
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
                [new Error('Value is invalid.', ['body', 'shipping', 'phone'])],
            ],
            [
                new Nested([
                    0 => new Nested([
                        0 => [new Number(min: -10, max: 10)],
                    ]),
                ]),
                [0 => [0 => -11]],
                [new Error($this->formatMessage('Value must be no less than {min}.', ['min' => -10]), [0, 0])],
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
                    requirePropertyPath: true,
                    noPropertyPathMessage: 'Property is not found.',
                ),
                [],
                [new Error('Property is not found.', ['value'])],
            ],
        ];
    }

    public function indexedByPathErrorMessagesProvider(): array
    {
        $requiredRule = new Required();
        $rule = new Nested(['value' => $requiredRule]);
        $value = [
            'author' => [
                'name' => 'Alex',
                'age' => 38,
            ],
        ];

        return [
            'error' => [
                new Nested(['author.age' => [new Number(min: 40)]]),
                $value,
                ['author.age' => [$this->formatMessage('Value must be no less than {min}.', ['min' => 40])]],
            ],
            'key not exists' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'])]]),
                $value,
                ['author.sex' => ['This value is invalid.']],
            ],
            [
                $rule,
                '',
                ['' => ['Value should be an array or an object. string given.']],
            ],
            [
                $rule,
                ['value' => null],
                ['value' => [$requiredRule->getMessage()]],
            ],
            [
                new Nested(['value' => new Required()], requirePropertyPath: true),
                [],
                ['value' => [$this->formatMessage($rule->getNoPropertyPathMessage(), ['path' => 'value'])]],
            ],
            [
                // @link https://github.com/yiisoft/validator/issues/200
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
                ['body.shipping.phone' => ['Value is invalid.']],
            ],
            [
                new Nested([
                    0 => new Nested([
                        0 => [new Number(min: -10, max: 10)],
                    ]),
                ]),
                [0 => [0 => -11]],
                ['0.0' => [$this->formatMessage('Value must be no less than {min}.', ['min' => -10])]],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new NestedHandler();
    }
}
