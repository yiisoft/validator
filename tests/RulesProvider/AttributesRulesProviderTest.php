<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\RulesProvider;

use JetBrains\PhpStorm\Deprecated;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;

final class AttributesRulesProviderTest extends TestCase
{
    public static function dataBase(): array
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
            'object with no properties' => [
                [],
                new class {},
            ],
            'object with properties without rule attributes' => [
                [
                    'title' => [Length::class],
                ],
                new class {
                    #[Deprecated(reason: 'test reason', replacement: 'test replacement')]
                    private int $viewsCount = 1;

                    private bool $active = true;

                    #[Length(max: 255)]
                    private string $title = 'Test title';
                },
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(array $expectedRuleClassNames, string|object $source): void
    {
        $rulesProvider = new AttributesRulesProvider($source);
        $ruleClassNames = $this->convertRulesToClassNames($rulesProvider->getRules());

        $this->assertSame($expectedRuleClassNames, $ruleClassNames);
    }

    public static function dataPropertyVisibility(): array
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

    #[DataProvider('dataPropertyVisibility')]
    public function testPropertyVisibility(
        array $expectedRuleClassNames,
        string|object $source,
        int $propertyVisibility,
    ): void {
        $rulesProvider = new AttributesRulesProvider($source, $propertyVisibility);

        $ruleClassNames = $this->convertRulesToClassNames($rulesProvider->getRules());

        $this->assertSame($expectedRuleClassNames, $ruleClassNames);
    }

    public static function dataStaticProperties(): array
    {
        return [
            [
                ['a' => [Required::class], 'b' => [TrueValue::class]],
                new class {
                    #[Required]
                    public int $a = 1;
                    #[TrueValue]
                    public static bool $b = false;
                },
                null,
            ],
            [
                ['a' => [Required::class]],
                new class {
                    #[Required]
                    public int $a = 1;
                    #[TrueValue]
                    public static bool $b = false;
                },
                true,
            ],
            [
                ['a' => [Required::class], 'b' => [TrueValue::class]],
                new class {
                    #[Required]
                    public int $a = 1;
                    #[TrueValue]
                    public static bool $b = false;
                },
                false,
            ],
        ];
    }

    #[DataProvider('dataStaticProperties')]
    public function testStaticProperties(
        array $expectedRuleClassNames,
        object $source,
        ?bool $skipStaticProperties,
    ): void {
        $rulesProvider = $skipStaticProperties === null
            ? new AttributesRulesProvider($source)
            : new AttributesRulesProvider($source, skipStaticProperties: $skipStaticProperties);

        $ruleClassNames = $this->convertRulesToClassNames($rulesProvider->getRules());

        $this->assertSame($expectedRuleClassNames, $ruleClassNames);
    }

    private function convertRulesToClassNames(iterable $rules): array
    {
        $classNames = [];
        foreach ($rules as $property => $propertyRules) {
            $classNames[$property] = array_map(
                static fn(RuleInterface $rule): string => $rule::class,
                $propertyRules instanceof Traversable ? iterator_to_array($propertyRules) : $propertyRules,
            );
        }

        return $classNames;
    }
}
