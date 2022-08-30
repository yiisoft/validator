<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\RulesProvider;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;

final class AttributesRulesProviderTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            'class-name' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
                ],
                ObjectWithDifferentPropertyVisibility::class,
            ],
            'object' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(
        array $expectedRuleClassNames,
        string|object $source
    ): void {
        $rulesProvider = new AttributesRulesProvider($source);

        $ruleClassNames = $this->convertRulesToClassNames($rulesProvider->getRules());

        $this->assertSame($expectedRuleClassNames, $ruleClassNames);
    }

    public function dataPropertyVisibility(): array
    {
        return [
            'class-name-and-private-protected-public' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
                ],
                ObjectWithDifferentPropertyVisibility::class,
                ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
            ],
            'class-name-and-private' => [
                [
                    'number' => [Number::class],
                ],
                ObjectWithDifferentPropertyVisibility::class,
                ReflectionProperty::IS_PRIVATE,
            ],
            'class-name-and-protected' => [
                [
                    'age' => [Number::class],
                ],
                ObjectWithDifferentPropertyVisibility::class,
                ReflectionProperty::IS_PROTECTED,
            ],
            'class-name-and-public' => [
                [
                    'name' => [Required::class],
                ],
                ObjectWithDifferentPropertyVisibility::class,
                ReflectionProperty::IS_PUBLIC,
            ],
            'object-and-private-protected-public' => [
                [
                    'name' => [Required::class],
                    'age' => [Number::class],
                    'number' => [Number::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
            ],
            'object-and-private' => [
                [
                    'number' => [Number::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PRIVATE,
            ],
            'object-and-protected' => [
                [
                    'age' => [Number::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PROTECTED,
            ],
            'object-and-public' => [
                [
                    'name' => [Required::class],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                ReflectionProperty::IS_PUBLIC,
            ],
        ];
    }

    /**
     * @dataProvider dataPropertyVisibility
     */
    public function testPropertyVisibility(
        array $expectedRuleClassNames,
        string|object $source,
        int $propertyVisibility,
    ): void {
        $rulesProvider = new AttributesRulesProvider($source, $propertyVisibility);

        $ruleClassNames = $this->convertRulesToClassNames($rulesProvider->getRules());

        $this->assertSame($expectedRuleClassNames, $ruleClassNames);
    }

    private function convertRulesToClassNames(array $rules): array
    {
        $classNames = [];
        foreach ($rules as $attribute => $attributeRules) {
            $classNames[$attribute] = array_map(
                static fn (RuleInterface $rule): string => get_class($rule),
                $attributeRules
            );
        }

        return $classNames;
    }
}
