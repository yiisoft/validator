<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTimeInterface;
use DateTimeImmutable;
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


    public function dataValidationPassed(): array
    {
        return [
            ['2024-08-15T15:52:01+00:00', [new DateTime(formats: DateTimeInterface::W3C)]],
            ['2024-08-15 15:52:01', [new DateTime(formats: 'Y-m-d H:i:s')]],
            ['15-08-2024 15:52', [new DateTime(formats: 'd-m-Y H:i')]],
            [
                '2024-08-15 15:52:01',
                [
                    new DateTime(
                        min: null,
                        max: null,
                        message: 'The value must be between 2024-08-15 15:52:01 and 2024-08-15 15:52:01.',
                        lessThanMinMessage: 'The value must be no less than 2024-08-15 15:52:01.',
                        greaterThanMaxMessage: 'The value must be no greater than 2024-08-15 15:52:01.',
                        skipOnEmpty: false,
                        skipOnError: false,
                        when: null,
                        formats: 'Y-m-d H:i:s', format: DateTimeInterface::W3C,
                    ),
                ],
            ],
            [
                '2024-08-15 15:52:01',
                [
                    new DateTime(
                        min: new DateTimeImmutable('2024-08-15 15:52:01'),
                        formats: 'Y-m-d H:i:s'
                    ),
                ],
            ],
            [
                new DateTimeImmutable('2024-08-15 15:52:01'),
                [new DateTime(formats: 'Y-m-d H:i:s')],
            ],
            [
                '2024-08-15 15:51:59',
                [
                    new DateTime(
                        max: new DateTimeImmutable('2024-08-15 15:52:01'),
                        formats: 'Y-m-d H:i:s'
                    ),
                ],
            ],
            [
                '2024-08-15 15:52:01',
                [
                    new DateTime(
                        max: new DateTimeImmutable('2024-08-15 15:52:01'),
                        formats: 'Y-m-d H:i:s'
                    ),
                ],
                ['' => [' must be no greater than 2024-08-15 15:52:01.']],
            ],

            [
                1705322898,
                [new DateTime()],
                ['' => [' value is not a valid DateTime.']],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [

            [
                '2023-02-20ee',
                [new DateTime(message: '{attribute} value is not a valid DateTime.')],
                ['' => [' value is not a valid DateTime.']],
            ],
            [
                '',
                [new DateTime(message: '{attribute} value is not a valid DateTime.')],
                ['' => [' value is not a valid DateTime.']],
            ],
            [
                '2024-08-14 15:52:01',
                [
                    new DateTime(
                        min: new DateTimeImmutable('2024-08-15 15:52:01'),
                        formats: 'Y-m-d H:i:s'
                    ),
                ],
                ['' => [' must be no less than 2024-08-15 15:52:01.']],
            ],
            [
                '2024-08-15 15:52:02',
                [
                    new DateTime(
                        max: new DateTimeImmutable('2024-08-15 15:52:01'),
                        formats: 'Y-m-d H:i:s'
                    ),
                ],
                ['' => [' must be no greater than 2024-08-15 15:52:01.']],
            ],
        ];
    }

    public function dataOptions(): array
    {
        return [
            [
                new DateTime(),
                [
                    'formats' => [],
                    'min' => null,
                    'max' => null,
                    'lessThanMinMessage' => [
                        'template' => '{attribute} must be no less than {min}.',
                        'parameters' => [],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => '{attribute} must be no greater than {max}.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{attribute} value is not a valid DateTime.',
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
