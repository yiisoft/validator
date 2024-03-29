<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTimeImmutable;
use DateTimeZone;
use Yiisoft\Validator\Rule\Date;
use Yiisoft\Validator\Rule\DateHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class DateTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
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
            'php-format' => ['2021-01-01', new Date(format: 'php:Y-m-d')],
            'intl-format' => ['2021-01-01', new Date(format: 'YYYY-mm-dd')],
            'datetime' => [new DateTimeImmutable('2021-01-01'), new Date()],
            'min' => ['2021-01-01', new Date(format: 'YYYY-mm-dd', min: '2020-01-01')],
            'max' => ['2021-01-01', new Date(format: 'YYYY-mm-dd', max: '2022-01-01')],
            'min-equal' => ['2021-01-01', new Date(format: 'YYYY-mm-dd', min: '2021-01-01')],
            'max-equal' => ['2021-01-01', new Date(format: 'YYYY-mm-dd', max: '2021-01-01')],
            'timezone' => [
                '12.11.2003, 15:00:00',
                new Date(
                    format: 'php:d.m.Y, H:i:s',
                    timeZone: 'UTC',
                    min: new DateTimeImmutable('12.11.2003, 16:00:00', new DateTimeZone('GMT+3')),
                ),
            ],
            'timestamp' => [1711705158, new Date(min: 1711705100)],
            'zero-time' => [
                '2021-01-01',
                new Date(format: 'php:Y-m-d', max: new DateTimeImmutable('2021-01-01, 00:00:00')),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $invalidDateMessage = ['' => ['Invalid date value.']];
        return [
            'php-format-invalid' => ['2021.01.01', new Date(format: 'php:Y-m-d'), $invalidDateMessage],
            'intl-format-invalid' => ['2021.01.01', new Date(format: 'YYYY-mm-dd'), $invalidDateMessage],
            'invalid-date' => ['2021.02.30', new Date(format: 'YYYY-mm-dd'), $invalidDateMessage],
            'min' => [
                '2024-03-29',
                new Date(format: 'YYYY-mm-dd', min: '2025-01-01'),
                ['' => ['The value must be no early than 2025-01-01.']],
            ],
            'max' => [
                '2024-03-29',
                new Date(format: 'php:Y-m-d', max: '2024-01-01'),
                ['' => ['The value must be no late than 2024-01-01.']],
            ],
            'timestamp' => [
                1711705158,
                new Date(format: 'php:d.m.Y, H:i:s', min: 1711705200),
                ['' => ['The value must be no early than 29.03.2024, 09:40:00.']],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Date::class, DateHandler::class];
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
