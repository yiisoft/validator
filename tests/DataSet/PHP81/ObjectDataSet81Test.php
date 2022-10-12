<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet\PHP81;

use PHPUnit\Framework\TestCase;
use Traversable;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Data\Charts\Chart;
use Yiisoft\Validator\Tests\Support\Data\TitleTrait;
use Yiisoft\Validator\Tests\Support\Rule\NotRuleAttribute;

final class ObjectDataSet81Test extends TestCase
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
                    #[Required(skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Required(skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    use TitleTrait;
                },
                [
                    'title' => [
                        new HasLength(max: 255),
                    ],
                ],
            ],
            [
                new class () {
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property1;
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property2;
                },
                [
                    'property1' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                    'property2' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Each([
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ])]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Each([
                            new Required(skipOnEmpty: true),
                            new HasLength(max: 255, skipOnEmpty: true),
                        ]),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Nested([
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ])]
                    #[Each([
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ])]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Nested([
                            new Required(skipOnEmpty: true),
                            new HasLength(max: 255, skipOnEmpty: true),
                        ]),
                        new Each([
                            new Required(skipOnEmpty: true),
                            new HasLength(max: 255, skipOnEmpty: true),
                        ]),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new HasLength(max: 255, skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: false),
                    ],
                ],
            ],
            [
                new class () {
                    #[Nested([
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ])]
                    #[Nested([
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ])]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Nested([
                            new Required(skipOnEmpty: true),
                            new HasLength(max: 255, skipOnEmpty: true),
                        ]),
                        new Nested([
                            new Required(skipOnEmpty: true),
                            new HasLength(max: 255, skipOnEmpty: true),
                        ]),
                        new HasLength(max: 255, skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: false),
                    ],
                ],
            ],
        ];
    }

    public function testMoreComplexEmbeddedRule(): void
    {
        $dataSet = new ObjectDataSet(new Chart());
        $secondEmbeddedRules = [
            'x' => [new Number(min: -10, max: 10)],
            'y' => [new Number(min: -10, max: 10)],
        ];
        $firstEmbeddedRules = [
            'coordinates' => new Nested(
                $secondEmbeddedRules,
                requirePropertyPath: true,
                noPropertyPathMessage: 'Custom message 4.'
            ),
            'rgb' => [
                new Count(exactly: 3),
                new Each(
                    [new Number(min: 0, max: 255)],
                    incorrectInputMessage: 'Custom message 5.',
                    message: 'Custom message 6.',
                ),
            ],
        ];

        $actualRules = $this->toNestedArray($dataSet->getRules());

        // check Chart structure has right structure
        $this->assertIsArray($actualRules);
        $this->assertArrayHasKey('points', $actualRules);
        $this->assertCount(1, $actualRules = $this->toArray($actualRules['points']));
        $this->assertInstanceOf(Each::class, $actualRules[0]);

        // check Chart structure has right structure
        $actualFirstEmbeddedRules = $this->toArray($actualRules[0]->getRules());
        $this->assertIsArray($actualFirstEmbeddedRules);
        $this->assertCount(1, $actualFirstEmbeddedRules);
        $this->assertInstanceOf(Nested::class, $actualFirstEmbeddedRules[0]);

        // check Point structure has right structure
        $innerRules = $this->toArray($actualFirstEmbeddedRules[0]->getRules());
        // rgb has usual structure. We can check as is
        $this->assertEquals($firstEmbeddedRules['rgb'], $this->toArray($innerRules['rgb']));

        // coordinates has embedded structure, so we need to unpack rules before check it
        $this->assertIsArray($innerRules = $this->toArray($innerRules['coordinates']));
        $this->assertCount(1, $innerRules);
        $this->assertInstanceOf(Each::class, $innerRules[0]);

        $secondInnerRules = $this->toArray($innerRules[0]->getRules());
        $this->assertIsArray($secondInnerRules);
        $this->assertCount(1, $secondInnerRules);
        $this->assertInstanceOf(Nested::class, $secondInnerRules[0]);
        $this->assertEquals($secondEmbeddedRules, $this->toNestedArray($secondInnerRules[0]->getRules()));
    }

    public function toArray(iterable $rules): array
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
