<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Date;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Date\DateTime;
use Yiisoft\Validator\Rule\Date\Date;
use Yiisoft\Validator\Rule\Date\DateTimeHandler;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class DateTimeTest extends RuleTestCase
{
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Date();
        $this->assertSame('date', $rule->getName());
    }

    public function dataValidationPassed(): array
    {
        return [
            'php-format' => ['2021-01-01, 12:35', new DateTime(format: 'php:Y-m-d, H:i')],
            'intl-format' => ['2021-01-01, 12:35', new DateTime(format: 'yyyy-MM-dd, HH:mm')],
            'datetime' => [new DateTimeImmutable('2021-01-01, 12:35'), new DateTime()],
            'min' => ['2021-01-01, 12:35', new DateTime(format: 'yyyy-MM-dd, HH:mm', min: '2020-01-01, 16:00')],
            'max' => ['2021-01-01, 12:35', new DateTime(format: 'yyyy-MM-dd, HH:mm', max: '2022-01-01, 16:00')],
            'min-equal' => ['2021-01-01, 12:35', new DateTime(format: 'yyyy-MM-dd, HH:mm', min: '2021-01-01, 12:35')],
            'max-equal' => ['2021-01-01, 12:35', new DateTime(format: 'yyyy-MM-dd, HH:mm', max: '2021-01-01, 12:35')],
            'timezone' => [
                '12.11.2003, 15:00:00',
                new DateTime(
                    format: 'php:d.m.Y, H:i:s',
                    timeZone: 'UTC',
                    min: new DateTimeImmutable('12.11.2003, 16:00:00', new DateTimeZone('GMT+3')),
                ),
            ],
            'timestamp' => [1711705158, new DateTime(min: 1711705100)],
            'rule-timezone-override-handler' => [
                '12.11.2003, 15:00:00',
                new DateTime(
                    format: 'php:d.m.Y, H:i:s',
                    timeZone: 'UTC',
                    min: new DateTimeImmutable('12.11.2003, 16:00:00', new DateTimeZone('GMT+3')),
                ),
                [DateTimeHandler::class => new DateTimeHandler(timeZone: 'GMT+3')],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $invalidDateMessage = ['' => ['Invalid date value.']];
        return [
            'php-format-invalid' => ['2021.01.01, 12:35', new DateTime(format: 'php:Y-m-d, H:i'), $invalidDateMessage],
            'php-format-invalid-2' => [
                '2021-17-35 16:60:97',
                new DateTime(format: 'php:Y-m-d H:i:s'),
                $invalidDateMessage,
            ],
            'intl-format-invalid' => [
                '2021.01.01, 12:35',
                new DateTime(format: 'yyyy-MM-dd, HH:mm'),
                $invalidDateMessage,
            ],
            'invalid-date' => ['2021.02.12, 25:24', new DateTime(format: 'yyyy-MM-dd, HH:mm'), $invalidDateMessage],
            'min' => [
                '2024-03-29, 12:35',
                new DateTime(format: 'yyyy-MM-dd, HH:mm', min: '2025-01-01, 11:00'),
                ['' => ['The value must be no early than 1/1/25, 11:00 AM.']],
            ],
            'max' => [
                '2024-03-29, 12:50',
                new DateTime(format: 'php:Y-m-d, H:i', max: '2024-01-01, 00:00'),
                ['' => ['The value must be no late than 1/1/24, 12:00 AM.']],
            ],
            'timestamp' => [
                1711705158,
                new DateTime(format: 'php:d.m.Y, H:i:s', min: 1711705200),
                ['' => ['The value must be no early than 3/29/24, 9:40 AM.']],
            ],
            'without-message-date-type' => [
                '29*03*2024*12*35',
                new DateTime(format: 'php:d*m*Y*12*35', max: '11*11*2023*12*35', dateType: null, timeType: null),
                ['' => ['The value must be no late than 11*11*2023*12*35.']],
                [DateTimeHandler::class => new DateTimeHandler(messageDateType: null, messageTimeType: null)],
            ],
            'message-type-edge-case' => [
                '29*03*2024*12*35',
                new DateTime(
                    format: 'php:d*m*Y*12*35',
                    max: '11*11*2023*12*35',
                    messageDateType: IntlDateFormatter::SHORT,
                    timeType: null
                ),
                ['' => ['The value must be no late than 11/11/23.']],
                [DateTimeHandler::class => new DateTimeHandler(messageDateType: null, messageTimeType: null)],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Date(), new Date(skipOnError: true));
    }

    public function testWhen(): void
    {
        $this->testWhenInternal(
            new Date(),
            new Date(
                when: static fn(mixed $value): bool => $value !== null
            )
        );
    }
}
