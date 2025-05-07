<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Uuid;
use Yiisoft\Validator\Rule\UuidHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class UuidTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Uuid();
        $this->assertSame(Uuid::class, $rule->getName());
    }

    public static function dataValidationPassed(): array
    {
        return [
            'regular uuid' => ['123e4567-e89b-12d3-a456-426614174000', [new Uuid()]],
            'nil uuid' => ['00000000-0000-0000-0000-000000000000', [new Uuid()]],
        ];
    }

    public static function dataValidationFailed(): array
    {
        return [
            'incorrect type, integer' => [12345, [new Uuid()], ['' => ['Value must be a string. int given.']]],
            'incorrect type, array' => [['123e4567-e89b-12d3-a456-426614174000'], [new Uuid()], ['' => ['Value must be a string. array given.']]],
            'invalid uuid format' => ['invalid-uuid-string', [new Uuid()], ['' => ['The value of value is not a valid UUID.']]],
            'too short' => ['123e4567-e89b', [new Uuid()], ['' => ['The value of value is not a valid UUID.']]],
            'wrong dashes' => ['123e4567e89b12d3a456426614174000', [new Uuid()], ['' => ['The value of value is not a valid UUID.']]],
            'letters instead of hex' => ['g23e4567-e89b-12d3-a456-426614174000', [new Uuid()], ['' => ['The value of value is not a valid UUID.']]],
            'empty string' => ['', [new Uuid()], ['' => ['The value of value is not a valid UUID.']]],

            'custom incorrect input message' => [
                123,
                [new Uuid(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                123,
                [new Uuid(incorrectInputMessage: 'Property - {Property}, property - {property}, type - {type}.')],
                ['' => ['Property - Value, property - value, type - int.']],
            ],
            'custom invalid UUID message' => [
                'invalid-uuid-string',
                [new Uuid(message: 'Custom invalid UUID message.')],
                ['' => ['Custom invalid UUID message.']],
            ],
            'custom invalid UUID message with parameters' => [
                ['childId' => 'invalid-uuid-string'],
                ['childId' => [new Uuid(message: 'Property - {Property}, property - {property}, value - {value}.')]],
                ['childId' => ['Property - ChildId, property - childId, value - invalid-uuid-string.']],
            ],
        ];
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new Uuid(),
                [
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be a string. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The value of {property} is not a valid UUID.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new Uuid(
                    incorrectInputMessage: 'Custom incorrect input message.',
                    message: 'Custom invalid UUID message.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'incorrectInputMessage' => [
                        'template' => 'Custom incorrect input message.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom invalid UUID message.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Uuid::class, UuidHandler::class];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Uuid(), new Uuid(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Uuid(), new Uuid(when: $when));
    }
}
