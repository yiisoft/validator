<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Date;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Date\Time;
use Yiisoft\Validator\Rule\Date\Date;
use Yiisoft\Validator\Rule\Date\TimeHandler;
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

    public static function dataValidationPassed(): array
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

    public static function dataValidationFailed(): array
    {
        $invalidDateMessage = ['' => ['Value must be a time.']];
        return [
            'php-format-invalid' => ['12-35', new Time(format: 'php:H:i'), $invalidDateMessage],
            'intl-format-invalid' => ['12-35', new Time(format: 'HH:mm'), $invalidDateMessage],
            'invalid-date' => ['25-35', new Time(format: 'HH:mm'), $invalidDateMessage],
            'min' => [
                '15:30',
                new Time(format: 'HH:mm', min: '15:40'),
                ['' => ['Value must be no earlier than 15:40.']],
            ],
            'max' => [
                '15:30',
                new Time(format: 'php:H:i', max: '12:00'),
                ['' => ['Value must be no later than 12:00.']],
            ],
            'handler-custom-message' => [
                '15:30',
                new Time(format: 'php:H:i', max: '12:00'),
                ['' => ['Max: 12:00.']],
                [TimeHandler::class => new TimeHandler(tooLateMessage: 'Max: {limit}.')],
            ],
            'handler-message-time-type-null-with-handler-custom-message' => [
                1711719000,
                new Time(
                    timeType: IntlDateFormatter::FULL,
                    max: 1711711800,
                    timeZone: 'UTC',
                    locale: 'en_US',
                ),
                ['' => ['Max: 11:30:00 AM Coordinated Universal Time.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: null, tooLateMessage: 'Max: {limit}.')],
            ],
            'timestamp' => [
                1711705158,
                new Time(format: 'php:d.m.Y, H:i:s', min: 1711705200),
                ['' => ['Value must be no earlier than 29.03.2024, 09:40:00.']],
            ],
            'without-message-time-type' => [
                '13*30',
                new Time(format: 'php:H*i', max: '11*30'),
                ['' => ['Value must be no later than 11*30.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: null)],
            ],
            'rule-message-format' => [
                '13*30',
                new Time(format: 'php:H*i', max: '11*30', messageFormat: 'php:H-i'),
                ['' => ['Value must be no later than 11-30.']],
                [TimeHandler::class => new TimeHandler(messageFormat: 'php:H_i')],
            ],
            'handler-message-type' => [
                1711719000,
                new Time(max: 1711711800, locale: 'en_US'),
                ['' => ['Value must be no later than 11:30:00 AM Coordinated Universal Time.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: IntlDateFormatter::FULL)],
            ],
            'handler-message-type-overrides-format' => [
                '13*30',
                new Time(format: 'php:H*i', max: '11*30'),
                ['' => ['Value must be no later than 11:30:00 AM Coordinated Universal Time.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: IntlDateFormatter::FULL)],
            ],
            'handler-time-type-does-not-affect-message' => [
                1711719000,
                new Time(
                    timeType: IntlDateFormatter::FULL,
                    max: 1711711800,
                    timeZone: 'UTC',
                    locale: 'en_US',
                ),
                ['' => ['Value must be no later than 11:30 AM.']],
                [TimeHandler::class => new TimeHandler(timeType: IntlDateFormatter::FULL)],
            ],
            'handler-message-time-type-null-falls-back-to-rule-time-type' => [
                1711719000,
                new Time(
                    timeType: IntlDateFormatter::FULL,
                    max: 1711711800,
                    timeZone: 'UTC',
                    locale: 'en_US',
                ),
                ['' => ['Value must be no later than 11:30:00 AM Coordinated Universal Time.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: null)],
            ],
            'handler-message-time-type-short-overrides-format' => [
                '15*30',
                new Time(format: 'php:H*i', max: '12*00'),
                ['' => ['Value must be no later than 12:00 PM.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: IntlDateFormatter::SHORT)],
            ],
            'rule-message-type-override-handler' => [
                '13*30',
                new Time(format: 'php:H*i', max: '11*30', messageTimeType: IntlDateFormatter::SHORT),
                ['' => ['Value must be no later than 11:30 AM.']],
                [TimeHandler::class => new TimeHandler(messageTimeType: IntlDateFormatter::FULL)],
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
                when: static fn(mixed $value): bool => $value !== null,
            ),
        );
    }
}
