<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;

final class RulesNormalizerTest extends TestCase
{
    public function dataNormalize(): array
    {
        return [
            'null' => [[], null],
            'default-property-visibility' => [
                [
                    'name' => ['required'],
                    'age' => ['number'],
                    'number' => ['number'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
            ],
            'private-properties' => [
                [
                    'number' => ['number'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PRIVATE,
            ],
            'protected-properties' => [
                [
                    'age' => ['number'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PROTECTED,
            ],
            'public-properties' => [
                [
                    'name' => ['required'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PUBLIC,
            ],
        ];
    }

    /**
     * @dataProvider dataNormalize
     *
     * More cases are covered in {@see ValidatorTest}.
     *
     * @see ValidatorTest::testDataAndRulesCombinations()
     * @see ValidatorTest::testRulesPropertyVisibility()
     * @see ValidatorTest::testWithEmptyArrayOfRules()
     * @see ValidatorTest::testDiverseTypes()
     * @see ValidatorTest::testNullAsDataSet()
     * @see ValidatorTest::testValidateWithSingleRule()
     */
    public function testNormalizeWithArrayResult(
        array $expected,
        iterable|object|string|null $rules,
        ?int $propertyVisibility = null,
        mixed $data = null
    ): void {
        $rules = $propertyVisibility === null
            ? RulesNormalizer::normalize(rules: $rules, data: $data)
            : RulesNormalizer::normalize(rules: $rules, propertyVisibility: $propertyVisibility, data: $data);

        $result = [];
        foreach ($rules as $attributeName => $attributeRules) {
            $result[$attributeName] = [];
            foreach ($attributeRules as $rule) {
                $result[$attributeName][] = $rule->getName();
            }
        }

        $this->assertSame($expected, $result);
    }
}
