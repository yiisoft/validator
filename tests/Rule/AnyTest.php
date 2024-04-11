<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Rule\Any;
use Yiisoft\Validator\Rule\AnyHandler;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRuleWithAfterInit;

final class AnyTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Any([new IntegerType(), new FloatType()]);
        $this->assertSame('any', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new Any([new IntegerType()]),
                [
                    'message' => [
                        'template' => 'At least one of the inner rules must pass the validation.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'integerType',
                            'message' => [
                                'template' => 'Value must be an integer.',
                                'parameters' => [],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
            'custom' => [
                new Any(
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
                            'integerType',
                            'message' => [
                                'template' => 'Value must be an integer.',
                                'parameters' => [],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                        [
                            'floatType',
                            'message' => [
                                'template' => 'Value must be a float.',
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

    public function dataValidationPassed(): array
    {
        return [
            'right away' => [1, new Any([new IntegerType(), new FloatType()])],
            'later' => [1.5, new Any([new IntegerType(), new FloatType()])],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'At least one of the inner rules must pass the validation.';

        return [
            'none' => ['1', new Any([new IntegerType(), new FloatType()]), ['' => [$message]]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(
            new Any([new IntegerType(), new FloatType()]),
            new Any([new IntegerType(), new FloatType()], skipOnError: true),
        );
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new Any([new IntegerType(), new FloatType()]),
            new Any([new IntegerType(), new FloatType()], when: $when),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Any::class, AnyHandler::class];
    }

    public function testAfterInitAttribute(): void
    {
        $object = new stdClass();
        $innerRule1 = new StubRuleWithAfterInit();
        $innerRule2 = new StubRuleWithAfterInit();

        (new Any([$innerRule1, $innerRule2]))->afterInitAttribute($object);
        $this->assertSame($object, $innerRule1->getObject());
        $this->assertSame($object, $innerRule2->getObject());
    }
}
