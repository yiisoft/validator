<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\ParametrizedRule;

class NestedTest extends TestCase
{
    public function validateDataProvider(): array
    {
        return [
            'success' => [
                [
                    'author.name' => [
                        new HasLength(min: 3),
                    ],
                ],
                true,
            ],
            'error' => [
                [
                    'author.age' => [
                        new Number(min: 20),
                    ],
                ],
                false,
            ],
            'key not exists' => [
                [
                    'author.sex' => [
                        new InRange(['male', 'female']),
                    ],
                ],
                false,
            ],
            'key not exists, skip empty' => [
                [
                    'author.sex' => [
                        new InRange(['male', 'female'], skipOnEmpty: true),
                    ],
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     *
     * @param Rule[] $rules
     */
    public function testValidate(array $rules, bool $expectedResult): void
    {
        $rule = new Nested($rules);
        $value = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];
        $result = $rule->validate($value);

        $this->assertEquals($expectedResult, $result->isValid());
    }

    public function testValidationEmptyRules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested([]);
    }

    public function testValidationRuleIsNotInstanceOfRule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested(['path.to.value' => (new stdClass())]);
    }

    public function testInvalidValue(): void
    {
        $rule = new Nested(['value' => new Required()]);
        $result = $rule->validate('');

        $this->assertEquals(['Value should be an array or an object. string given.'], $result->getErrorMessages());
    }

    public function testValidationMessage(): void
    {
        $rule = new Nested(['value' => new Required()]);
        $result = $rule->validate(['value' => null]);

        $this->assertEquals(['Value cannot be blank.'], $result->getErrorMessages());
    }

    public function testErrorWhenValuePathNotFound(): void
    {
        $rule = new Nested(['value' => new Required()], throwErrorWhenPropertyPathIsNotFound: true);
        $result = $rule->validate([]);

        $this->assertEquals(['Property path "value" is not found.'], $result->getErrorMessages());
    }

    public function testPropertyPathIsNotFoundMessage(): void
    {
        $rule = new Nested(
            ['value' => new Required()],
            throwErrorWhenPropertyPathIsNotFound: true,
            propertyPathIsNotFoundMessage: 'Property is not found.',
        );
        $result = $rule->validate([]);

        $this->assertEquals(['Property is not found.'], $result->getErrorMessages());
    }

    public function testGetName(): void
    {
        $rule = new Nested(['value' => new Required()]);
        $this->assertEquals('nested', $rule->getName());
    }

    public function getOptionsDataProvider(): array
    {
        return [
            [
                [
                    'author.name' => new ParametrizedRule('author-name', ['key' => 'name']),
                    'author.age' => new ParametrizedRule('author-age', ['key' => 'age']),
                ],
                [
                    'author.name' => ['key' => 'name'],
                    'author.age' => ['key' => 'age'],
                ],
            ],
            [
                [
                    'author' => [
                        'name' => new ParametrizedRule('author-name', ['key' => 'name']),
                        'age' => new ParametrizedRule('author-age', ['key' => 'age']),
                    ],
                ],
                [
                    'author' => [
                        'name' => ['key' => 'name'],
                        'age' => ['key' => 'age'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsDataProvider
     */
    public function testGetOptions(array $rules, array $expectedOptions): void
    {
        $rule = new Nested($rules);
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }

    public function testWithOtherNestedAndEach(): void
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
        $rule = new Nested([
            'charts' => [
                new Each([new Nested([
                    'points' => [
                        new Each([new Nested([
                            'coordinates' => new Nested([
                                'x' => [
                                    new Number(min: -10, max: 10),
                                    new Callback(static function ($value): Result {
                                        $result = new Result();
                                        $result->addError('Custom error.');

                                        return $result;
                                    }),
                                ],
                                'y' => [new Number(min: -10, max: 10)],
                            ]),
                            'rgb' => [
                                new Count(exactly: 3),
                                new Each([new Number(min: 0, max: 255)]),
                            ],
                        ])]),
                    ],
                ])]),
            ],
        ]);
        $result = $rule->validate($data);

        $expectedDetailedErrorsData = [
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
        $expectedDetailedErrors = [];
        foreach ($expectedDetailedErrorsData as $errorData) {
            $expectedDetailedErrors[] = new Error($errorData[1], $errorData[0]);
        }

        $this->assertEquals($expectedDetailedErrors, $result->getErrors());

        $expectedErrorMessages = [
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
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());

        $expectedErrorMessagesIndexedByPath = [
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
        $this->assertEquals($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testIntValuePath(): void
    {
        $rule = new Nested([
            0 => new Nested([
                0 => [new Number(min: -10, max: 10)],
            ]),
        ]);
        $result = $rule->validate([0 => [0 => -11]]);

        $this->assertCount(1, $result->getErrors());
        $this->assertSame($result->getErrors()[0]->getValuePath(), [0, 0]);
    }

    public function testSeparateErrorGroups(): void
    {
        $rule = new Nested([
            'key' => [
                new Each([
                    new HasLength(min: 5),
                    new InRange(['aaa', 'bbb', 'ccc']),
                ]),
            ],
        ]);
        $result = $rule->validate(['key' => ['x', 'y']]);
        $indexedErrors = $result->getErrorMessagesIndexedByPath();

        $this->assertSame(array_keys($indexedErrors), ['key.0', 'key.1']);
        $this->assertSame(array_keys($indexedErrors['key.0']), [0, 1]);
        $this->assertSame(array_keys($indexedErrors['key.1']), [0, 1]);
        $this->assertIsString($indexedErrors['key.0'][0]);
        $this->assertIsString($indexedErrors['key.0'][1]);
        $this->assertIsString($indexedErrors['key.1'][0]);
        $this->assertIsString($indexedErrors['key.1'][1]);
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/200
     */
    public function testCombineDotNotationAndNestedStructure(): void
    {
        $rule = new Nested([
            'body.shipping' => [
                new Required(),
                new Nested([
                    'phone' => [new Regex('/^\+\d{11}$/')],
                ]),
            ],
        ]);
        $data = [
            'body' => [
                'shipping' => [
                    'phone' => '+777777777777',
                ],
            ],
        ];
        $result = $rule->validate($data);
        $expectedErrors = [new Error('Value is invalid.', ['body', 'shipping', 'phone'])];

        $this->assertEquals($expectedErrors, $result->getErrors());
    }
}
