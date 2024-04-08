<?php

declare(strict_types=1);

namespace Rule\Type;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Type\BooleanType;
use Yiisoft\Validator\Rule\Type\BooleanTypeHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class BooleanTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new BooleanType();
        $this->assertSame('booleanType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new BooleanType(),
                [
                    'message' => [
                        'template' => 'Value must be a boolean.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new BooleanType(message: 'Custom message.', skipOnError: true, skipOnEmpty: true),
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
            [false, new BooleanType()],
            [true, new BooleanType()],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a boolean.';

        return [
            [0.0, new BooleanType(), ['' => [$message]]],
            [0, new BooleanType(), ['' => [$message]]],
            [1, new BooleanType(), ['' => [$message]]],
            ['0', new BooleanType(), ['' => [$message]]],
            ['1', new BooleanType(), ['' => [$message]]],
            ['false', new BooleanType(), ['' => [$message]]],
            ['true', new BooleanType(), ['' => [$message]]],
            [[], new BooleanType(), ['' => [$message]]],
            'message, custom' => [['active' => []], ['active' => new BooleanType('{attribute}')], ['active' => ['active']]],
            'message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public ?bool $active = null,
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'active' => 'Активен',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'active' => new BooleanType(message: '"{attribute}" - не булево значение.'),
                        ];
                    }
                },
                null,
                ['active' => ['"Активен" - не булево значение.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new BooleanType(), new BooleanType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new BooleanType(), new BooleanType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [BooleanType::class, BooleanTypeHandler::class];
    }
}
