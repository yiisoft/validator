<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\DataSet;

use PHPUnit\Framework\TestCase;
use Traversable;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Data\TitleTrait;
use Yiisoft\Validator\Tests\Support\Rule\NotRuleAttribute;
use Yiisoft\Validator\Tests\TestEnvironments\Support\Data\Charts\Chart;

final class ObjectDataSetTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param RuleInterface[]|RuleInterface[][]|RuleInterface[][][] $expectedRules
     */
    public function testCollectRules(object $object, array $expectedRules): void
    {
        $dataSet = new ObjectDataSet($object);

        $rulesArray = $this->toNestedArray($dataSet->getRules());

        $this->assertEquals($expectedRules, $rulesArray);
    }

    public function dataProvider(): array
    {
        return [
            [
                new class () {
                },
                [],
            ],
            [
                new class () {
                    private $property1;
                },
                [],
            ],
            [
                new class () {
                    #[NotRuleAttribute]
                    private $property1;
                },
                [],
            ],
            [
                new class () {
                    #[Required()]
                    private $property1;
                },
                [
                    'property1' => [
                        new Required(),
                    ],
                ],
            ],
            [
                new class () {
                    use TitleTrait;
                },
                [
                    'title' => [
                        new Length(max: 255),
                    ],
                ],
            ],
            [
                new class () {
                    #[Required()]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property1;
                    #[Required()]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property2;
                },
                [
                    'property1' => [
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                    'property2' => [
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Each([
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ])]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Each([
                            new Required(),
                            new Length(max: 255, skipOnEmpty: true),
                        ]),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Nested([
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ])]
                    #[Each([
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ])]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Nested([
                            new Required(),
                            new Length(max: 255, skipOnEmpty: true),
                        ]),
                        new Each([
                            new Required(),
                            new Length(max: 255, skipOnEmpty: true),
                        ]),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Length(max: 255, skipOnEmpty: true)]
                    #[Length(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Length(max: 255, skipOnEmpty: true),
                        new Length(max: 255, skipOnEmpty: false),
                    ],
                ],
            ],
            [
                new class () {
                    #[Nested([
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ])]
                    #[Nested([
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ])]
                    #[Length(max: 255, skipOnEmpty: true)]
                    #[Length(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Nested([
                            new Required(),
                            new Length(max: 255, skipOnEmpty: true),
                        ]),
                        new Nested([
                            new Required(),
                            new Length(max: 255, skipOnEmpty: true),
                        ]),
                        new Length(max: 255, skipOnEmpty: true),
                        new Length(max: 255, skipOnEmpty: false),
                    ],
                ],
            ],
        ];
    }

    public function testMoreComplexEmbeddedRule(): void
    {
        $dataSet = new ObjectDataSet(new Chart());
        $expectedRules = [
            'points' => [
                new Each([
                    new Nested([
                        'coordinates' => new Each([
                            new Nested(
                                [
                                    'x' => [new Number(min: -10, max: 10)],
                                    'y' => [new Number(min: -10, max: 10)],
                                ],
                                requirePropertyPath: true,
                                noPropertyPathMessage: 'Custom message 4.',
                            ),
                        ]),
                        'rgb' => [
                            new Count(3),
                            new Each(
                                [new Number(min: 0, max: 255)],
                                incorrectInputMessage: 'Custom message 5.',
                            ),
                        ],
                    ]),
                ]),
            ],
        ];
        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    private function toArray(iterable $rules): array
    {
        return $rules instanceof Traversable ? iterator_to_array($rules) : (array) $rules;
    }

    private function toNestedArray(iterable $doubleIterable): array
    {
        $actualRules = [];
        foreach ($doubleIterable as $key => $iterable) {
            $actualRules[$key] = $this->toArray($iterable);
        }

        return $actualRules;
    }
}
