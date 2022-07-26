<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet\PHP81;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Attribute\Embedded;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Data\Charts\Chart;
use Yiisoft\Validator\Tests\Data\Charts\Coordinate;
use Yiisoft\Validator\Tests\Data\TitleTrait;
use Yiisoft\Validator\Tests\Stub\NotRuleAttribute;

final class AttributeDataSet81Test extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param RuleInterface[]|RuleInterface[][]|RuleInterface[][][] $expectedRules
     */
    public function testCollectRules(object $object, array $expectedRules): void
    {
        $dataSet = new AttributeDataSet($object);

        $this->assertEquals($expectedRules, $dataSet->getRules());
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

    /**
     * The test flow is different because {@see Embedded} is only attribute.
     * Under the hood it uses {@see Composite} and a bit of reflection.
     * Due to we cannot create {@see Embedded} with particular rules via constructor
     * we cannot just compare it with another class that extends {@see Composite}.
     */
    public function testEmbeddedAttribute(): void
    {
        $object = new class () {
            #[Embedded(Coordinate::class)]
            private $property1;
        };
        $expectedExtendedRules = [
            'x' => [new Number(min: -10, max: 10)],
            'y' => [new Number(min: -10, max: 10)],
        ];

        $dataSet = new AttributeDataSet($object);

        $actualRules = $dataSet->getRules();

        $this->assertIsArray($actualRules);
        $this->assertArrayHasKey('property1', $actualRules);
        $this->assertIsArray($actualRules['property1']);
        $this->assertCount(1, $actualRules['property1']);
        $this->assertInstanceOf(Composite::class, $actualRules['property1'][0]);
        $this->assertEquals($expectedExtendedRules, $actualRules['property1'][0]->getRules());
    }

    public function testMoreComplexEmbeddedRule(): void
    {
        $dataSet = new AttributeDataSet(new Chart());
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

        $actualRules = $dataSet->getRules();

        // check Chart structure has right structure
        $this->assertIsArray($actualRules);
        $this->assertArrayHasKey('points', $actualRules);
        $this->assertIsArray($actualRules['points']);
        $this->assertCount(1, $actualRules['points']);
        $this->assertInstanceOf(Each::class, $actualRules['points'][0]);

        // check Chart structure has right structure
        $actualFirstEmbeddedRules = $actualRules['points'][0]->getRules();
        $this->assertIsArray($actualFirstEmbeddedRules);
        $this->assertCount(1, $actualFirstEmbeddedRules);
        $this->assertInstanceOf(Composite::class, $actualFirstEmbeddedRules[0]);

        // check Point structure has right structure
        $innerRules = $actualFirstEmbeddedRules[0]->getRules();
        // rgb has usual structure. We can check as is
        $this->assertEquals($firstEmbeddedRules['rgb'], $innerRules['rgb']);

        // coordinates has embedded structure, so we need to unpack rules before check it
        $this->assertIsArray($innerRules['coordinates']);
        $this->assertCount(1, $innerRules['coordinates']);
        $this->assertInstanceOf(Each::class, $innerRules['coordinates'][0]);

        $secondInnerRules = $innerRules['coordinates'][0]->getRules();
        $this->assertIsArray($secondInnerRules);
        $this->assertCount(1, $secondInnerRules);
        $this->assertInstanceOf(Composite::class, $secondInnerRules[0]);
        $this->assertEquals($secondEmbeddedRules, $secondInnerRules[0]->getRules());
    }
}
