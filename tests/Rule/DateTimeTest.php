<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\DateTime;
use Yiisoft\Validator\Rule\DateTimeHandler;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;

final class DateTimeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function dataInvalidConfiguration(): array
    {
        return [
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
        new DateTime(...$arguments);
    }

    public function dataValidationPassed(): array
    {
        return [
            ['2020-01-01', [new DateTime(format: 'Y-m-d')]],
            ['2020-01-01 10:10:10', [new DateTime(format: 'Y-m-d H:i:s')]],
            ['10.02.2023', [new DateTime(format: 'd.m.Y')]],
            ['10/02/2023', [new DateTime(format: 'd/m/Y')]],
            ['April 30, 2023, 5:16 pm', [new DateTime(format: 'F j, Y, g:i a')]],
            ['', [new DateTime(format: 'd-m-Y', skipOnEmpty: true)]],
            ['125636000', [new DateTime(format: 'U')]],
            [125636000, [new DateTime(format: 'U')]],
            [123456.344, [new DateTime(format: 'U.u')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'incorrect input, is boolean' => [
                true,
                [new DateTime(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            [
                '2023-02-20ee',
                [new DateTime(format: 'Y-m-dee',)],
                ['' => ['The  is not a valid date.']],
            ],
            [
                '2024-02-20',
                [new DateTime(format: 'H:i',)],
                ['' => ['The  is not a valid date.']],
            ],
            [
                '2023-02-30',
                [new DateTime(format: 'Y-m-d', message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - 2023-02-30.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['attribute' => []],
                ['attribute' => [new DateTime(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')]],
                ['attribute' => ['Attribute - attribute, type - array.']],
            ],
            'incorrect input, is not date' => [
                'datetime',
                [new DateTime(message: 'Attribute - {attribute}, value - {value}.')],
                ['' => ['Attribute - , value - datetime.']],
            ],
            'empty string and custom message' => [
                '',
                [new DateTime()],
                ['' => ['The  must be a date.']],
            ],
              [
                null,
                [new DateTime()],
                ['' => ['The  must be a date.']],
            ],
        ];
    }

    public function dataOptions(): array
    {
        return [
            [
                new DateTime(),
                [
                    'format' => 'Y-m-d',
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
        $this->testSkipOnErrorInternal(new DateTime(), new DateTime(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new DateTime(), new DateTime(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [DateTime::class, DateTimeHandler::class];
    }

    public function testGetName(): void
    {
        $rule = new DateTime();
        $this->assertSame(DateTime::class, $rule->getName());
    }

}
