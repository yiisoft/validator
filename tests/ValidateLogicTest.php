<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Stub\EachNestedObjects\Foo;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Stub\ObjectWithRulesProvider;
use Yiisoft\Validator\Validator;

final class ValidateLogicTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'pure-object-and-array-of-rules' => [
                [
                    'number' => ['Value must be no less than 77.'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                [
                    'age' => new Number(max: 100),
                    'number' => new Number(min: 77),
                ],
            ],
            'pure-object-and-no-rules' => [
                [
                    'name' => ['Value cannot be blank.'],
                    'age' => ['Value must be no less than 21.'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                null,
            ],
            'dataset-object-and-array-of-rules' => [
                [
                    'key1' => ['Value must be no less than 21.'],
                ],
                new ObjectWithDataSet(),
                [
                    'key1' => new Number(min: 21),
                ],
            ],
            'dataset-object-and-no-rules' => [
                [],
                new ObjectWithDataSet(),
                null,
            ],
            'rules-provider-object-and-array-of-rules' => [
                [
                    'number' => ['Value must be no greater than 7.'],
                ],
                new ObjectWithRulesProvider(),
                [
                    'age' => new Number(max: 100),
                    'number' => new Number(max: 7),
                ],
            ],
            'rules-provider-object-and-no-rules' => [
                [
                    'age' => ['Value must be equal to "25".'],
                ],
                new ObjectWithRulesProvider(),
                null,
            ],
            'rules-provider-and-dataset-object-and-array-of-rules' => [
                [
                    'key2' => ['Value must be no greater than 7.'],
                ],
                new ObjectWithDataSetAndRulesProvider(),
                [
                    'key2' => new Number(max: 7),
                ],
            ],
            'rules-provider-and-dataset-object-and-no-rules' => [
                [
                    'key2' => ['Value must be equal to "99".'],
                ],
                new ObjectWithDataSetAndRulesProvider(),
                null,
            ],
            'array-and-array-of-rules' => [
                [
                    'key2' => ['Value must be no greater than 7.'],
                ],
                ['key1' => 15, 'key2' => 99],
                [
                    'key1' => new Number(max: 100),
                    'key2' => new Number(max: 7),
                ],
            ],
            'array-and-no-rules' => [
                [],
                ['key1' => 15, 'key2' => 99],
                null,
            ],
            'scalar-and-array-of-rules' => [
                [
                    '' => ['Value must be no greater than 7.'],
                ],
                42,
                [
                    new Number(max: 7),
                ],
            ],
            'scalar-and-no-rules' => [
                [],
                42,
                null,
            ],
            'array-and-rules-provider' => [
                [
                    'age' => ['Value must be no less than 18.'],
                ],
                [
                    'age' => 17,
                ],
                new class () implements RulesProviderInterface {
                    public function getRules(): iterable
                    {
                        return [
                            'age' => [new Number(min: 18)],
                        ];
                    }
                },
            ],
            'array-and-object' => [
                [
                    'name' => ['Value not passed.'],
                    'bars' => ['Value must be array or iterable.'],
                ],
                [],
                new Foo(),
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(array $expectedErrorMessages, mixed $data, iterable|object|string|null $rules): void
    {
        $result = $this->createValidator()->validate($data, $rules);
        $this->assertSame($expectedErrorMessages, $result->getErrorMessagesIndexedByAttribute());
    }

    public function dataWithEmptyArrayOfRules(): array
    {
        return [
            'pure-object-and-no-rules' => [new ObjectWithDifferentPropertyVisibility()],
            'dataset-object-and-no-rules' => [new ObjectWithDataSet()],
            'rules-provider-object' => [new ObjectWithRulesProvider()],
            'rules-provider-and-dataset-object' => [new ObjectWithDataSetAndRulesProvider()],
            'array' => [['key1' => 15, 'key2' => 99]],
            'scalar' => [42],
        ];
    }

    /**
     * @dataProvider dataWithEmptyArrayOfRules
     */
    public function testWithEmptyArrayOfRules(mixed $data): void
    {
        $result = $this->createValidator()->validate($data, []);

        $this->assertTrue($result->isValid());
    }

    private function createValidator(): Validator
    {
        return FakeValidatorFactory::make();
    }
}
