<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Type;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Rule\Type\IntegerTypeHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class IntegerTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new IntegerType();
        $this->assertSame('integerType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new IntegerType(),
                [
                    'message' => [
                        'template' => 'Value must be an integer.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new IntegerType(message: 'Custom message.', skipOnEmpty: true, skipOnError: true),
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
            'integer, negative' => [-1, new IntegerType()],
            'integer, zero' => [0, new IntegerType()],
            'integer, positive' => [1, new IntegerType()],
            'octal number' => [0123, new IntegerType()],
            'hexademical number' => [0x1A, new IntegerType()],
            'binary number' => [0b11111111, new IntegerType()],
            'decimal number, underscores, PHP >= 7.4' => [1_234_567, new IntegerType()],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be an integer.';

        return [
            'boolean' => [false, new IntegerType(), ['' => [$message]]],
            'float' => [1.5, new IntegerType(), ['' => [$message]]],
            'string containing float' => ['1.5', new IntegerType(), ['' => [$message]]],
            'string containing integer' => ['1', new IntegerType(), ['' => [$message]]],
            'array' => [[], new IntegerType(), ['' => [$message]]],
            'message, custom' => [
                ['sum' => []],
                ['sum' => new IntegerType('Attribute - {attribute}, type - {type}')],
                ['sum' => ['Attribute - sum, type - array']],
            ],
            'message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public ?int $sum = null,
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
                            'sum' => new IntegerType(message: '"{attribute}" - нецелое число.'),
                        ];
                    }
                },
                null,
                ['sum' => ['"Сумма" - нецелое число.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new IntegerType(), new IntegerType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new IntegerType(), new IntegerType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [IntegerType::class, IntegerTypeHandler::class];
    }
}
