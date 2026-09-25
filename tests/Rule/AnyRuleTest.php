<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\AnyRule;
use Yiisoft\Validator\Rule\AnyRuleHandler;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRuleWithAfterInit;
use Yiisoft\Validator\Tests\Support\Data\PostValidationHookCounter;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class AnyRuleTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new AnyRule([new IntegerType(), new FloatType()]);
        $this->assertSame(AnyRule::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new AnyRule([new IntegerType()]),
                [
                    'message' => [
                        'template' => 'At least one of the inner rules must pass the validation.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            IntegerType::class,
                            'message' => [
                                'template' => '{Property} must be an integer.',
                                'parameters' => [],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
            'custom' => [
                new AnyRule(
                    [new IntegerType(), new FloatType()],
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'message' => [
                        'template' => 'Custom message.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                    'rules' => [
                        [
                            IntegerType::class,
                            'message' => [
                                'template' => '{Property} must be an integer.',
                                'parameters' => [],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                        [
                            FloatType::class,
                            'message' => [
                                'template' => '{Property} must be a float.',
                                'parameters' => [],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            'right away' => [1, new AnyRule([new IntegerType(), new FloatType()])],
            'later' => [1.5, new AnyRule([new IntegerType(), new FloatType()])],
            'using as attribute' => [
                new class {
                    #[AnyRule([new IntegerType(), new FloatType()])]
                    private int|float $sum = 1.5;
                },
                null,
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $message = 'At least one of the inner rules must pass the validation.';

        return [
            'none' => ['1', new AnyRule([new IntegerType(), new FloatType()]), ['' => [$message]]],
            'using as attribute' => [
                new class {
                    #[AnyRule([new IntegerType(), new FloatType()])]
                    private string $sum = '1.5';
                },
                null,
                ['sum' => [$message]],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(
            new AnyRule([new IntegerType(), new FloatType()]),
            new AnyRule([new IntegerType(), new FloatType()], skipOnError: true),
        );
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new AnyRule([new IntegerType(), new FloatType()]),
            new AnyRule([new IntegerType(), new FloatType()], when: $when),
        );
    }

    public function testAfterInitAttribute(): void
    {
        $object = new stdClass();
        $innerRule1 = new StubRuleWithAfterInit();
        $innerRule2 = new StubRuleWithAfterInit();

        (new AnyRule([$innerRule1, $innerRule2]))->afterInitAttribute($object);
        $this->assertSame($object, $innerRule1->getObject());
        $this->assertSame($object, $innerRule2->getObject());
    }

    public function testDataSetAndPropertyInInnerRules(): void
    {
        $data = ['a' => 'x', 'b' => 1];
        $innerData = null;
        $innerProperty = null;

        (new Validator())->validate($data, [
            'a' => new AnyRule([
                new Callback(
                    static function (mixed $value, Callback $rule, ValidationContext $context) use (
                        &$innerData,
                        &$innerProperty,
                    ): Result {
                        $innerData = $context->getDataSet()->getData();
                        $innerProperty = $context->getProperty();
                        return new Result();
                    },
                ),
            ]),
        ]);

        $this->assertSame($data, $innerData);
        $this->assertSame('a', $innerProperty);
    }

    public function testDataSetInInnerRulesWithNestedEach(): void
    {
        $data = [
            'groups' => [
                ['items' => [['a' => 1], ['a' => 2]]],
                ['items' => [['a' => 3]]],
            ],
        ];

        $createRules = static function (callable $wrap) use (&$innerData): array {
            $callback = new Callback(
                static function (mixed $value, Callback $rule, ValidationContext $context) use (&$innerData): Result {
                    $innerData[] = [$context->getDataSet()->getData(), $context->getProperty()];
                    return new Result();
                },
            );
            return [
                'groups' => new Each(
                    new Nested([
                        'items' => new Each(
                            new Nested(['a' => $wrap($callback)]),
                        ),
                    ]),
                ),
            ];
        };

        $innerData = [];
        (new Validator())->validate(
            $data,
            $createRules(static fn(Callback $callback): Callback => $callback),
        );
        $expectedInnerData = $innerData;

        $innerData = [];
        (new Validator())->validate(
            $data,
            $createRules(static fn(Callback $callback): AnyRule => new AnyRule([$callback])),
        );

        $this->assertSame(
            [
                [['a' => 1], 'a'],
                [['a' => 2], 'a'],
                [['a' => 3], 'a'],
            ],
            $expectedInnerData,
        );
        $this->assertSame($expectedInnerData, $innerData);
    }

    public function testObjectValueInInnerRules(): void
    {
        $result = (new Validator())->validate(
            ['o' => (object) ['x' => 7]],
            ['o' => new AnyRule([new Each([new Number(max: 10)])])],
        );

        $this->assertSame(
            ['o' => ['At least one of the inner rules must pass the validation.']],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testPostValidationHookOfDataInInnerRules(): void
    {
        $data = new PostValidationHookCounter();

        (new Validator())->validate(
            $data,
            [new AnyRule([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(1, $data->hookCallsCount);
    }

    public function testPostValidationHookOfPropertyValueInInnerRules(): void
    {
        $value = new PostValidationHookCounter();

        (new Validator())->validate(
            ['o' => $value],
            ['o' => new AnyRule([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(0, $value->hookCallsCount);
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [AnyRule::class, AnyRuleHandler::class];
    }
}
