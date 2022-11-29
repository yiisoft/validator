<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\RegexHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class RegexTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Regex('//');
        $this->assertSame('regex', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Regex('//'),
                [
                    'pattern' => '//',
                    'not' => false,
                    'incorrectInputMessage' => [
                        'template' => 'Value should be string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Value is invalid.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Regex('//', not: true),
                [
                    'pattern' => '//',
                    'not' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Value should be string.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Value is invalid.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            ['a', [new Regex('/a/')]],
            ['ab', [new Regex('/a/')]],
            ['b', [new Regex('/a/', not: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputMessage = 'Value should be string.';
        $message = 'Value is invalid.';

        return [
            [['a', 'b'], [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [['a', 'b'], [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            'custom incorrect input message' => [
                null,
                [new Regex('/a/', incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                null,
                [new Regex('/a/', incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - null.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => null],
                ['data' => new Regex('/a/', incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - null.']],
            ],

            ['b', [new Regex('/a/')], ['' => [$message]]],

            'custom message' => ['b', [new Regex('/a/', message: 'Custom message.')], ['' => ['Custom message.']]],
            'custom message with parameters' => [
                'b',
                [new Regex('/a/', message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - b.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 'b'],
                ['data' => new Regex('/a/', message: 'Attribute - {attribute}, value - {value}.')],
                ['data' => ['Attribute - data, value - b.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Regex('//'), new Regex('//', skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Regex('//'), new Regex('//', when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Regex::class, RegexHandler::class];
    }
}
