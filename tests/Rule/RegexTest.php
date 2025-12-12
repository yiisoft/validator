<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\RegexHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class RegexTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testNumberEmptyPattern(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pattern can\'t be empty.');
        new Regex(pattern: '');
    }

    public function testGetName(): void
    {
        $rule = new Regex('//');
        $this->assertSame(Regex::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new Regex('//'),
                [
                    'pattern' => '//',
                    'not' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be a string. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Property} is invalid.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new Regex(
                    '//',
                    not: true,
                    incorrectInputMessage: 'Custom message 1.',
                    message: 'Custom message 2.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'pattern' => '//',
                    'not' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
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
            ['a', [new Regex('/a/')]],
            ['ab', [new Regex('/a/')]],
            ['b', [new Regex('/a/', not: true)]],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $message = 'Value is invalid.';

        return [
            [['a', 'b'], [new Regex('/a/')], ['' => ['Value must be a string. array given.']]],
            [['a', 'b'], [new Regex('/a/', not: true)], ['' => ['Value must be a string. array given.']]],
            [null, [new Regex('/a/')], ['' => ['Value must be a string. null given.']]],
            [null, [new Regex('/a/', not: true)], ['' => ['Value must be a string. null given.']]],
            [new stdClass(), [new Regex('/a/')], ['' => ['Value must be a string. stdClass given.']]],
            [new stdClass(), [new Regex('/a/', not: true)], ['' => ['Value must be a string. stdClass given.']]],
            'not' => ['a', [new Regex('/a/', not: true)], ['' => [$message]]],
            'custom incorrect input message' => [
                null,
                [new Regex('/a/', incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                null,
                [new Regex('/a/', incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - null.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['data' => null],
                ['data' => new Regex('/a/', incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['data' => ['Property - data, type - null.']],
            ],

            ['b', [new Regex('/a/')], ['' => [$message]]],

            'custom message' => ['b', [new Regex('/a/', message: 'Custom message.')], ['' => ['Custom message.']]],
            'custom message with parameters' => [
                'b',
                [new Regex('/a/', message: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - b.']],
            ],
            'custom message with parameters, property set' => [
                ['data' => 'b'],
                ['data' => new Regex('/a/', message: 'Property - {Property}, value - {value}.')],
                ['data' => ['Property - Data, value - b.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Regex('//'), new Regex('//', skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Regex('//'), new Regex('//', when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Regex::class, RegexHandler::class];
    }
}
