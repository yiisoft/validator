<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Data\Post;

final class AttributeDataSetTest extends TestCase
{
//    public function testGetRules(): void
//    {
//        $dataSet = new AttributeDataSet(new ChartsData());
//        $expectedRules = [
//            'charts' => [
//                new Each([
//                    new Nested([
//                        'points' => [
//                            new Each([new Nested([
//                                'coordinates' => new Nested(
//                                    [
//                                        'x' => [new Number(min: -10, max: 10)],
//                                        'y' => [new Number(min: -10, max: 10)],
//                                    ],
//                                    errorWhenPropertyPathIsNotFound: true,
//                                    propertyPathIsNotFoundMessage: 'Custom message 4.'
//                                ),
//                                'rgb' => [
//                                    new Count(exactly: 3),
//                                    new Each([
//                                        new Number(min: 0, max: 255),
//                                    ], incorrectInputMessage: 'Custom message 5.', message: 'Custom message 6.'),
//                                ],
//                            ])]),
//                        ],
//                    ], errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 3.'),
//                ], incorrectInputMessage: 'Custom message 1.', message: 'Custom message 2.'),
//            ],
//        ];
//
//        $this->assertEquals($expectedRules, $dataSet->getRules());
//    }

    /**
     * @param object $object
     * @param RuleInterface[]|RuleInterface[][]|RuleInterface[][][] $rules
     * @return void
     * @dataProvider dataProvider
     */
    public function testCollectRules(object $object, array $expectedRules): void
    {
        $dataSet = new AttributeDataSet($object);

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/198
     */
    public function testGetRulesViaTraits(): void
    {
        $dataSet = new AttributeDataSet(new Post());
        $expectedRules = ['title' => [new HasLength(max: 255)]];

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    public function dataProvider()
    {
        return [
            [
                new class {
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
                new class {
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(skipOnEmpty: true)]
                    private $property1;
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(skipOnEmpty: true)]
                    private $property2;
                },
                [
                    'property1' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(skipOnEmpty: true),
                    ],
                    'property2' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class {
                    #[Each([
                        new Required(skipOnEmpty: true),
                        new HasLength(skipOnEmpty: true),
                    ])]
                    #[HasLength(skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Each([
                            new Required(skipOnEmpty: true),
                            new HasLength(skipOnEmpty: true),
                        ]),
                        new HasLength(skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class {
                    #[Nested([
                        new Required(skipOnEmpty: true),
                        new HasLength(skipOnEmpty: true),
                    ])]
                    #[Each([
                        new Required(skipOnEmpty: true),
                        new HasLength(skipOnEmpty: true),
                    ])]
                    #[HasLength(skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Nested([
                            new Required(skipOnEmpty: true),
                            new HasLength(skipOnEmpty: true),
                        ]),
                        new Each([
                            new Required(skipOnEmpty: true),
                            new HasLength(skipOnEmpty: true),
                        ]),
                        new HasLength(skipOnEmpty: true),
                    ],
                ],
            ],
        ];
    }
}
