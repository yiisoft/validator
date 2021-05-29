<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
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

        $this->assertEquals(['Value should be an array or an object. string given.'], $result->getErrors());
    }

    public function testValidationMessage(): void
    {
        $validator = Nested::rule([
            'value' => Required::rule()->message('Value cannot be blank.'),
        ]);

        $result = $validator->validate(['value' => null]);

        $this->assertEquals(['Value cannot be blank.'], $result->getErrors());
    }

    public function testErrorWhenValuePathNotFound(): void
    {
        $validator = Nested::rule(['value' => Required::rule()])
            ->errorWhenPropertyPathIsNotFound(true);

        $result = $validator->validate([]);

        $this->assertEquals(['Property path "value" is not found.'], $result->getErrors());
    }

    public function testPropertyPathIsNotFoundMessage(): void
    {
        $validator = Nested::rule(['value' => Required::rule()])
            ->errorWhenPropertyPathIsNotFound(true)
            ->propertyPathIsNotFoundMessage('Property is not found.');

        $result = $validator->validate([]);

        $this->assertEquals(['Property is not found.'], $result->getErrors());
    }

    public function testName(): void
    {
        $validator = Nested::rule(['value' => Required::rule()]);
        $this->assertEquals('nested', $validator->getName());
    }

    /**
     * @dataProvider optionsDataProvider()
     */
    public function testOptions(array $rules, array $expectedOptions): void
    {
        $validator =Nested::rule($rules);

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
}
