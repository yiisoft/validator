<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;

final class RulesNormalizerTest extends TestCase
{
    public function dataNormalize(): array
    {
        return [
            'null' => [[], null],
            'object' => [
                [
                    'name' => ['required'],
                    'age' => ['number'],
                    'number' => ['number'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
            ],
            'class-string' => [
                [
                    'name' => ['required'],
                    'age' => ['number'],
                    'number' => ['number'],
                ],
                ObjectWithDifferentPropertyVisibility::class,
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
        callable|iterable|object|string|null $rules,
        mixed $data = null
    ): void {
        $rules = RulesNormalizer::normalize($rules, $data);

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
