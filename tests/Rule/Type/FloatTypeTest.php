<?php

declare(strict_types=1);

namespace Rule\Type;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
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
        $this->assertSame('floatType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new FloatType(),
                [
                    'message' => [
                        'template' => 'Value must be a float.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new FloatType(message: 'Custom message.', skipOnError: true, skipOnEmpty: true),
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

    public function dataValidationPassed(): array
    {
        return [
            'float, negative' => [-1.5, new FloatType()],
            'float, zero' => [0.0, new FloatType()],
            'float, positive' => [1.5, new FloatType()],
            'float, e' => [1.2e3, new FloatType()],
            'float, capital e' => [7E-10, new FloatType()],
            'float, underscores, PHP >= 7.4' => [1_234.567, new FloatType()],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a float.';

        return [
            'boolean' => [false, new FloatType(), ['' => [$message]]],
            'integer, negative' => [- 1, new FloatType(), ['' => [$message]]],
            'integer, zero' => [0, new FloatType(), ['' => [$message]]],
            'integer, positive' => [1, new FloatType(), ['' => [$message]]],
            'string containing float' => ['1.5', new FloatType(), ['' => [$message]]],
            'array' => [[], new FloatType(), ['' => [$message]]],
            'message, custom' => [
                ['sum' => []],
                ['sum' => new FloatType('{attribute}')],
                ['sum' => ['sum']],
            ],
            'message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public ?bool $active = null,
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'sum' => 'Сумма',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'sum' => new FloatType(message: '"{attribute}" - невещественное число.'),
                        ];
                    }
                },
                null,
                ['sum' => ['"Сумма" - невещественное число.']],
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
