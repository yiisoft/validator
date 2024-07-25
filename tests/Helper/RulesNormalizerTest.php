<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;

final class RulesNormalizerTest extends TestCase
{
    public function dataNormalize(): array
    {
        return [
            'null' => [[], null],
            'object' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
            ],
            'class-string' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
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
        foreach ($rules as $propertyName => $propertyRules) {
            $result[$propertyName] = [];
            foreach ($propertyRules as $rule) {
                $result[$propertyName][] = $rule->getName();
            }
        }

        $this->assertSame($expected, $result);
    }

    public function dataNormalizeList(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [Callback::class],
                static fn () => new Result(),
            ],
            [
                [Number::class],
                new Number(),
            ],
            [
                [Number::class, Callback::class],
                [new Number(), static fn () => new Result()],
            ],
        ];
    }

    /**
     * @dataProvider dataNormalizeList
     */
    public function testNormalizeList(array $expected, iterable|callable|RuleInterface $rules): void
    {
        $rules = RulesNormalizer::normalizeList($rules);

        $result = [];
        foreach ($rules as $rule) {
            $result[] = $rule->getName();
        }

        $this->assertSame($expected, $result);
    }
}
