<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet\PHP80;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Traversable;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Data\Post;
use Yiisoft\Validator\Tests\Data\TitleTrait;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\NotRuleAttribute;
use Yiisoft\Validator\Tests\Stub\ObjectWithCallbackMethod;
use Yiisoft\Validator\Tests\Stub\ObjectWithNonExistingCallbackMethod;
use Yiisoft\Validator\Tests\Stub\ObjectWithNonPublicCallbackMethod;
use Yiisoft\Validator\Tests\Stub\ObjectWithNonStaticCallbackMethod;

final class ObjectDataSet80Test extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param RuleInterface[]|RuleInterface[][]|RuleInterface[][][] $expectedRules
     */
    public function testCollectRules(object $object, array $expectedRules): void
    {
        $dataSet = new ObjectDataSet($object);

        $actualRules = [];
        foreach ($dataSet->getRules() as $attribute => $rules) {
            $actualRules[$attribute] = $rules instanceof Traversable ? iterator_to_array($rules) : (array) $rules;
        }

        $this->assertEquals($expectedRules, $actualRules);
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

    /**
     * @link https://github.com/yiisoft/validator/issues/198
     */
    public function testGetRulesViaTraits(): void
    {
        $dataSet = new ObjectDataSet(new Post());
        $expectedRules = ['title' => [new HasLength(max: 255)]];

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/223
     */
    public function testValidateWithCallbackMethod(): void
    {
        $dataSet = new ObjectDataSet(new ObjectWithCallbackMethod());
        $validator = FakeValidatorFactory::make();

        /** @var array $rules */
        $rules = $dataSet->getRules();
        $this->assertSame(['name'], array_keys($rules));
        $this->assertCount(1, $rules['name']);
        $this->assertInstanceOf(Callback::class, $rules['name'][0]);

        $result = $validator->validate(['name' => 'bar'], $rules);
        $this->assertSame(['name' => ['Value must be "foo"!']], $result->getErrorMessagesIndexedByPath());
    }

    public function validateWithWrongCallbackMethodDataProvider(): array
    {
        return [
            [new ObjectWithNonExistingCallbackMethod()],
            [new ObjectWithNonPublicCallbackMethod()],
            [new ObjectWithNonStaticCallbackMethod()],
        ];
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/223
     * @dataProvider validateWithWrongCallbackMethodDataProvider
     */
    public function testValidateWithWrongCallbackMethod(object $object): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method must exist and have public and static modifers.');
        new ObjectDataSet($object);
    }
}
