<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet\PHP80;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Data\TitleTrait;
use Yiisoft\Validator\Tests\Stub\NotRuleAttribute;

final class AttributeDataSet80Test extends TestCase
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
        ];
    }
}
