<?php

declare(strict_types=1);

namespace Rule\Type;

use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\FloatTypeHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class FloatTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new FloatType();
        $this->assertSame(FloatType::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new FloatType(),
                [
                    'message' => [
                        'template' => '{Property} must be a float.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new FloatType(message: 'Custom message.', skipOnEmpty: true, skipOnError: true),
                [
                    'message' => [
                        'template' => 'Custom message.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            'float, negative' => [-1.5, new FloatType()],
            'float, zero' => [0.0, new FloatType()],
            'float, positive' => [1.5, new FloatType()],
            'float, e' => [1.2e3, new FloatType()],
            'float, capital e' => [7E-10, new FloatType()],
            'float, underscores, PHP >= 7.4' => [1_234.567, new FloatType()],
            'using as attribute' => [
                new class () {
                    #[FloatType]
                    private float $sum = 1.5;
                },
                null,
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $message = 'Value must be a float.';

        return [
            'boolean' => [false, new FloatType(), ['' => [$message]]],
            'integer, negative' => [-1, new FloatType(), ['' => [$message]]],
            'integer, zero' => [0, new FloatType(), ['' => [$message]]],
            'integer, positive' => [1, new FloatType(), ['' => [$message]]],
            'string containing float' => ['1.5', new FloatType(), ['' => [$message]]],
            'array' => [[], new FloatType(), ['' => [$message]]],
            'message, custom' => [
                ['sum' => []],
                ['sum' => new FloatType('Property - {property}, type - {type}')],
                ['sum' => ['Property - sum, type - array']],
            ],
            'message, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public ?float $sum = null,
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'sum' => 'Сумма',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'sum' => new FloatType(message: '"{property}" - невещественное число.'),
                        ];
                    }
                },
                null,
                ['sum' => ['"Сумма" - невещественное число.']],
            ],
            'using as attribute' => [
                new class () {
                    #[FloatType]
                    private int $sum = 1;
                },
                null,
                ['sum' => ['Sum must be a float.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new FloatType(), new FloatType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new FloatType(), new FloatType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [FloatType::class, FloatTypeHandler::class];
    }
}
