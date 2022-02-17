<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\Tests\Stub\ParametrizedRule;

/**
 * @group validators
 */
class NestedTest extends TestCase
{
    /**
     * @dataProvider validateDataProvider
     *
     * @param Rule[] $rules
     * @param bool $expectedResult
     */
    public function testValidate(array $rules, bool $expectedResult): void
    {
        $values = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        $actualResult = Nested::rule($rules)->validate($values);

        $this->assertEquals($expectedResult, $actualResult->isValid());
    }

    public function testValidationEmptyRules(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Nested::rule([]);
    }

    public function testValidationRuleIsNotInstanceOfRule(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Nested::rule(['path.to.value' => (new \stdClass())]);
    }

    public function testInvalidValue(): void
    {
        $validator = Nested::rule(['value' => Required::rule()]);

        $result = $validator->validate('');

        $this->assertEquals(['Value should be an array or an object. string given.'], $result->getErrorMessages());
    }

    public function testValidationMessage(): void
    {
        $validator = Nested::rule([
            'value' => Required::rule()->message('Value cannot be blank.'),
        ]);

        $result = $validator->validate(['value' => null]);

        $this->assertEquals(['Value cannot be blank.'], $result->getErrorMessages());
    }

    public function testErrorWhenValuePathNotFound(): void
    {
        $validator = Nested::rule(['value' => Required::rule()])
            ->errorWhenPropertyPathIsNotFound(true);

        $result = $validator->validate([]);

        $this->assertEquals(['Property path "value" is not found.'], $result->getErrorMessages());
    }

    public function testPropertyPathIsNotFoundMessage(): void
    {
        $validator = Nested::rule(['value' => Required::rule()])
            ->errorWhenPropertyPathIsNotFound(true)
            ->propertyPathIsNotFoundMessage('Property is not found.');

        $result = $validator->validate([]);

        $this->assertEquals(['Property is not found.'], $result->getErrorMessages());
    }

    public function testName(): void
    {
        $validator = Nested::rule(['value' => Required::rule()]);
        $this->assertEquals('nested', $validator->getName());
    }

    /**
     * @dataProvider optionsDataProvider
     */
    public function testOptions(array $rules, array $expectedOptions): void
    {
        $validator = Nested::rule($rules);

        $options = $validator->getOptions();

        $this->assertEquals($expectedOptions, $options);
    }

    public function optionsDataProvider(): array
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

    public function validateDataProvider(): array
    {
        return [
            'success' => [
                [
                    'author.name' => [
                        HasLength::rule()->min(3),
                    ],
                ],
                true,
            ],
            'error' => [
                [
                    'author.age' => [
                        Number::rule()->min(20),
                    ],
                ],
                false,
            ],
            'key not exists' => [
                [
                    'author.sex' => [
                        InRange::rule(['male', 'female']),
                    ],
                ],
                false,
            ],
            'key not exists, skip empty' => [
                [
                    'author.sex' => [
                        InRange::rule(['male', 'female'])->skipOnEmpty(true),
                    ],
                ],
                true,
            ],
        ];
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
        $rule = Nested::rule([
            'charts' => Each::rule(new RuleSet([
                Nested::rule([
                    'points' => Each::rule(new RuleSet([
                        Nested::rule([
                            'coordinates' => Nested::rule([
                                'x' => [
                                    Number::rule()->min(-10)->max(10),
                                    Callback::rule(static function ($value): Result {
                                        $result = new Result();
                                        $result->addError('Custom error.');

                                        return $result;
                                    }),
                                ],
                                'y' => [Number::rule()->min(-10)->max(10)],
                            ]),
                            'rgb' => Each::rule(new RuleSet([
                                Number::rule()->min(0)->max(255),
                            ])),
                        ]),
                    ])),
                ]),
            ])),
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
        $this->assertEquals([
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
        ], $result->getErrorMessages());
        $this->assertEquals([
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
        ], $result->getErrorMessagesIndexedByPath());
    }

    public function testIntValuePath(): void
    {
        $rule = Nested::rule([
            0 => Nested::rule([
                0 => [Number::rule()->min(-10)->max(10)],
            ]),
        ]);
        $result = $rule->validate([0 => [0 => -11]]);

        $this->assertCount(1, $result->getErrors());
        $this->assertSame($result->getErrors()[0]->getValuePath(), [0, 0]);
    }

    public function testSeparateErrorGroups(): void
    {
        $rule = Nested::rule([
            'key' => Each::rule(new RuleSet([
                HasLength::rule()->min(5),
                InRange::rule(['aaa', 'bbb', 'ccc']),
            ])),
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
}
