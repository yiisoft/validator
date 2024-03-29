<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Date;

use DateTimeImmutable;
use DateTimeZone;
use Yiisoft\Validator\Rule\Date\Time;
use Yiisoft\Validator\Rule\Date\Date;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class TimeTest extends RuleTestCase
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
            'php-format' => ['12:34', new Time(format: 'php:H:i')],
            'intl-format' => ['12:30', new Time(format: 'HH:mm')],
            'datetime' => [new DateTimeImmutable('2021-01-01, 12:34'), new Time()],
            'min' => ['12:00', new Time(format: 'HH:mm', min: '11:00')],
            'max' => ['12:00', new Time(format: 'HH:mm', max: '13:00')],
            'min-equal' => ['12:00', new Time(format: 'HH:mm', min: '12:00')],
            'max-equal' => ['12:00', new Time(format: 'HH:mm', max: '12:00')],
            'timezone' => [
                '15:00:00',
                new Time(
                    format: 'php:H:i:s',
                    timeZone: 'UTC',
                    min: new DateTimeImmutable('12.11.2100, 16:00:00', new DateTimeZone('GMT+3')),
                ),
            ],
            'timestamp' => [1711705158, new Time(min: 1711705100)],
        ];
    }

    public function dataValidationFailed(): array
    {
        $invalidDateMessage = ['' => ['Invalid time value.']];
        return [
            'php-format-invalid' => ['12-35', new Time(format: 'php:H:i'), $invalidDateMessage],
            'intl-format-invalid' => ['12-35', new Time(format: 'HH:mm'), $invalidDateMessage],
            'invalid-date' => ['25-35', new Time(format: 'HH:mm'), $invalidDateMessage],
            'min' => [
                '15:30',
                new Time(format: 'HH:mm', min: '15:40'),
                ['' => ['The value must be no early than 3:40 PM.']],
            ],
            'max' => [
                '15:30',
                new Time(format: 'php:H:i', max: '12:00'),
                ['' => ['The value must be no late than 12:00 PM.']],
            ],
            'timestamp' => [
                1711705158,
                new Time(format: 'php:d.m.Y, H:i:s', min: 1711705200),
                ['' => ['The value must be no early than 9:40 AM.']],
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
