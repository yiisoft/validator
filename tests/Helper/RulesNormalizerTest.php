<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Helper\RulesNormalizer;

final class RulesNormalizerTest extends TestCase
{
    public function dataNormalize(): array
    {
        return [
            'null' => [[], null],
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
        iterable|object|null $rules,
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

    public function testInvalidRules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A rules object must implement RulesProviderInterface or RuleInterface.');
        RulesNormalizer::normalize(new stdClass());
    }
}
