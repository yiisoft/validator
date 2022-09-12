<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleHandlerInterface;

use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\ObjectWithNestedObject;

use function array_slice;

final class NestedHandlerTest extends AbstractRuleValidatorTest
{
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
                ['author.age' => ['Value must be no less than 40.']],
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
                ['value' => ['Value cannot be blank.']],
            ],
            [
                new Nested(['value' => new Required()], requirePropertyPath: true),
                [],
                ['value' => ['Property path "value" is not found.']],
            ],
            // https://github.com/yiisoft/validator/issues/200
            [
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
                ['0.0' => ['Value must be no less than -10.']],
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
                [new Error('Value must be no less than 20.', ['author', 'age'])],
            ],
            'key not exists' => [
                new Nested(['author.sex' => [new InRange(['male', 'female'])]]),
                $value,
                [new Error('This value is invalid.', ['author', 'sex'])],
            ],
            [
                $rule,
                '',
                [new Error('Value should be an array or an object. string given.')],
            ],
            [
                $rule,
                ['value' => null],
                [new Error('Value cannot be blank.', ['value'])],
            ],
            [
                new Nested(['value' => new Required()], requirePropertyPath: true),
                [],
                [new Error('Property path "value" is not found.', ['value'])],
            ],
            [
                // https://github.com/yiisoft/validator/issues/200
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
                [new Error('Value must be no less than -10.', [0, 0])],
            ],
            [
                new Nested(['author\.data.name\.surname' => [new HasLength(min: 8)]]),
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
                [new Error('This value must contain at least 8 characters.', ['author.data', 'name.surname'])],
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
            'keys containing separator, one nested rule' => [
                new Nested([
                    'author\.data.name\.surname' => [
                        new HasLength(min: 3),
                    ],
                ]),
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
            ],
            'keys containing separator, multiple nested rules' => [
                new Nested([
                    'author\.data' => new Nested([
                        'name\.surname' => [
                            new HasLength(min: 3),
                        ],
                    ]),
                ]),
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
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

    public function withOtherNestedAndEachDataProvider(): array
    {
        $data = [
            'charts' => [
                [
                    'points' => [
                        ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                        ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
                    ],
                ],
                [
                    'points' => [
                        ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                        ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
                    ],
                ],
                [
                    'points' => [
                        ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                        ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
                    ],
                ],
            ],
        ];
        $xRules = [
            new Number(min: -10, max: 10),
            new Callback(static function ($value): Result {
                $result = new Result();
                $result->addError('Custom error.');

                return $result;
            }),
        ];
        $yRules = [new Number(min: -10, max: 10)];
        $rgbRules = [
            new Count(exactly: 3),
            new Each([new Number(min: 0, max: 255)]),
        ];

        $detailedErrorsData = [
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 0, 'rgb', 0], 'Value must be no less than 0. -1 given.'],
            [['charts', 0, 'points', 0, 'rgb', 1], 'Value must be no greater than 255. 256 given.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 1, 'rgb', 1], 'Value must be no less than 0. -2 given.'],
            [['charts', 0, 'points', 1, 'rgb', 2], 'Value must be no greater than 255. 257 given.'],
            [['charts', 1, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 1, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 0, 'rgb', 0], 'Value must be no less than 0. -3 given.'],
            [['charts', 2, 'points', 0, 'rgb', 1], 'Value must be no greater than 255. 258 given.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 1, 'rgb', 1], 'Value must be no less than 0. -4 given.'],
            [['charts', 2, 'points', 1, 'rgb', 2], 'Value must be no greater than 255. 259 given.'],
        ];
        $detailedErrors = [];
        foreach ($detailedErrorsData as $errorData) {
            $detailedErrors[] = new Error($errorData[1], $errorData[0]);
        }

        $errorMessages = [
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -1 given.',
            'Value must be no greater than 255. 256 given.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -2 given.',
            'Value must be no greater than 255. 257 given.',
            'Custom error.',
            'Custom error.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -3 given.',
            'Value must be no greater than 255. 258 given.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -4 given.',
            'Value must be no greater than 255. 259 given.',
        ];
        $errorMessagesIndexedByPath = [
            'charts.0.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.0.rgb.0' => ['Value must be no less than 0. -1 given.'],
            'charts.0.points.0.rgb.1' => ['Value must be no greater than 255. 256 given.'],
            'charts.0.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.1.rgb.1' => ['Value must be no less than 0. -2 given.'],
            'charts.0.points.1.rgb.2' => ['Value must be no greater than 255. 257 given.'],
            'charts.1.points.0.coordinates.x' => ['Custom error.'],
            'charts.1.points.1.coordinates.x' => ['Custom error.'],
            'charts.2.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.0.rgb.0' => ['Value must be no less than 0. -3 given.'],
            'charts.2.points.0.rgb.1' => ['Value must be no greater than 255. 258 given.'],
            'charts.2.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.1.rgb.1' => ['Value must be no less than 0. -4 given.'],
            'charts.2.points.1.rgb.2' => ['Value must be no greater than 255. 259 given.'],
        ];

        return [
            'base' => [
                $data,
                new Nested([
                    'charts' => [
                        new Each([new Nested([
                            'points' => [
                                new Each([new Nested([
                                    'coordinates' => new Nested([
                                        'x' => $xRules,
                                        'y' => $yRules,
                                    ]),
                                    'rgb' => $rgbRules,
                                ])]),
                            ],
                        ])]),
                    ],
                ]),
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            // https://github.com/yiisoft/validator/issues/195
            'withShortcut' => [
                $data,
                new Nested([
                    'charts.*.points.*.coordinates.x' => $xRules,
                    'charts.*.points.*.coordinates.y' => $yRules,
                    'charts.*.points.*.rgb' => $rgbRules,
                ]),
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            'withShortcutAndGrouping' => [
                $data,
                new Nested([
                    'charts.*.points.*.coordinates' => new Nested([
                        'x' => $xRules,
                        'y' => $yRules,
                    ]),
                    'charts.*.points.*.rgb' => $rgbRules,
                ]),
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            'withShortcutAndKeysContainingSeparatorAndShortcut' => [
                [
                    'charts.list' => [
                        [
                            'points*list' => [
                                [
                                    'coordinates.data' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0],
                                ],
                            ],
                        ],
                    ],
                ],
                new Nested([
                    'charts\.list.*.points\*list.*.coordinates\.data.x' => $xRules,
                    'charts\.list.*.points\*list.*.coordinates\.data.y' => $yRules,
                    'charts\.list.*.points\*list.*.rgb' => $rgbRules,
                ]),
                [
                    new Error(
                        message: $errorMessages[0],
                        valuePath: ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'x']
                    ),
                    new Error(
                        message: $errorMessages[1],
                        valuePath: ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'x']
                    ),
                    new Error(
                        message: $errorMessages[2],
                        valuePath: ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'y']
                    ),
                    new Error(
                        message: $errorMessages[3],
                        valuePath: ['charts.list', 0, 'points*list', 0, 'rgb', 0]
                    ),
                    new Error(
                        message: $errorMessages[4],
                        valuePath: ['charts.list', 0, 'points*list', 0, 'rgb', 1]
                    ),
                ],
                array_slice($errorMessages, 0, 5),
                [
                    'charts\.list.0.points\*list.0.coordinates\.data.x' => [$errorMessages[0], $errorMessages[1]],
                    'charts\.list.0.points\*list.0.coordinates\.data.y' => [$errorMessages[2]],
                    'charts\.list.0.points\*list.0.rgb.0' => [$errorMessages[3]],
                    'charts\.list.0.points\*list.0.rgb.1' => [$errorMessages[4]],
                ],
            ],
        ];
    }

    /**
     * @dataProvider withOtherNestedAndEachDataProvider
     */
    public function testWithOtherNestedAndEach(
        array $data,
        Nested $rule,
        array $expectedDetailedErrors,
        array $expectedErrorMessages,
        array $expectedErrorMessagesIndexedByPath
    ): void {
        $result = $this->validate($data, $rule);

        $this->assertEquals($expectedDetailedErrors, $result->getErrors());
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());
        $this->assertEquals($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testNestedWithoutRulesWithObject(): void
    {
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate(new ObjectWithNestedObject());

        $this->assertFalse($result->isValid());
        $this->assertSame([
            'caption' => ['This value must contain at least 3 characters.'],
            'object.name' => ['This value must contain at least 5 characters.'],
        ], $result->getErrorMessagesIndexedByPath());
    }

    public function testPropagateOptions(): void
    {
        $rule = new Nested([
            'posts' => [
                new Each([new Nested([
                    'title' => [new HasLength(min: 3)],
                    'authors' => [
                        new Each([new Nested([
                            'name' => [new HasLength(min: 5)],
                            'age' => [
                                new Number(min: 18),
                                new Number(min: 20),
                            ],
                        ])]),
                    ],
                ])]),
            ],
            'meta' => [new HasLength(min: 7)],
        ], propagateOptions: true, skipOnEmpty: true, skipOnError: true);
        $options = $rule->getOptions();
        $paths = [
            [],
            ['rules', 'posts', 0],
            ['rules', 'posts', 0, 'rules', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'title', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'name', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'age', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'age', 1],
            ['rules', 'meta', 0],
        ];
        $keys = ['skipOnEmpty', 'skipOnError'];

        foreach ($paths as $path) {
            foreach ($keys as $key) {
                $fullPath = $path;
                $fullPath[] = $key;

                $value = ArrayHelper::getValueByPath($options, $fullPath);
                $this->assertTrue($value);
            }
        }
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new NestedHandler($this->getTranslator());
    }
}
