<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Date;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\DateHandler;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;

final class DateTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function dataInvalidConfiguration(): array
    {
        return [
            [['pattern' => ''], 'Pattern can\'t be empty.'],
            [['format' => ''], 'Format can\'t be empty.'],
        ];
    }

    /**
     * @dataProvider dataInvalidConfiguration
     */
    public function testinvalidConfiguration(array $arguments, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Date(...$arguments);
    }

    public function dataValidationPassed(): array
    {
        return [
            ['2020-01-01', [new Date(format: 'Y-m-d')]],
            ['10.02.2023', [new Date(format: 'd.m.Y')]],
            ['10/02/2023', [new Date(format: 'd/m/Y')]],
            ['', [new Date(format: 'd-m-Y', skipOnEmpty: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'incorrect input, is integer' => [
                1,
                [new Date(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            [
                '2023-02-30',
                [new Date(format: 'Y-m-d', message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - 2023-02-30.']],
            ],
            [
                '2023-02-20ee',
                [new Date(format: 'Y-m-dee', incorrectInputMessage: 'The must be a date.')],
                ['' => ['The must be a date.']],
            ],

            [
                '10-02-2023 00:00',
                [new Date(format: 'd-m-Y H:i', incorrectInputMessage: 'The must be a date.')],
                ['' => ['The must be a date.']],
            ],
            [
                '2023-02-2023',
                [new Date(format: 'Y-d-Y', incorrectInputMessage: 'The must be a date.')],
                ['' => ['The must be a date.']],
            ],
            'incorrect input, is not date' => [
                'asdadas',
                [new Date(message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - asdadas.']],
            ],
            'empty string and custom message' => [
                '',
                [new Date(message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
        ];
    }

    public function dataOptions(): array
    {
        return [
            [
                new Date(),
                [
                    'format' => 'Y-m-d',
                    'pattern' => '/^(?=.*Y)(?=.*[mM])(?=.*d).*[Ymd](-|\/|.)[Ymd]\1[Ymd]$/',
                    'incorrectInputMessage' => [
                        'template' => 'The {attribute} must be a date.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The {attribute} is not a valid date.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Date(), new Date(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Date(), new Date(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Date::class, DateHandler::class];
    }

    public function testGetName(): void
    {
        $rule = new Date();
        $this->assertSame(Date::class, $rule->getName());
    }

}
