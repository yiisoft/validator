<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Date;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use LogicException;
use stdClass;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Rule\Date\Date;
use Yiisoft\Validator\Rule\Date\DateHandler;
use Yiisoft\Validator\Rule\Date\DateTime;
use Yiisoft\Validator\Rule\Date\Time;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class DateTest extends RuleTestCase
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
            'php-format' => ['2021-01-01', new Date(format: 'php:Y-m-d')],
            'intl-format' => ['2021-01-01', new Date(format: 'yyyy-MM-dd')],
            'datetime' => [new DateTimeImmutable('2021-01-01'), new Date()],
            'min' => ['2021-01-01', new Date(format: 'yyyy-MM-dd', min: '2020-01-01')],
            'max' => ['2021-01-01', new Date(format: 'yyyy-MM-dd', max: '2022-01-01')],
            'min-equal' => ['2021-01-01', new Date(format: 'yyyy-MM-dd', min: '2021-01-01')],
            'max-equal' => ['2021-01-01', new Date(format: 'yyyy-MM-dd', max: '2021-01-01')],
            'timezone' => [
                '12.11.2003',
                new Date(
                    format: 'php:d.m.Y',
                    timeZone: 'UTC',
                    min: new DateTimeImmutable('12.11.2003, 1:00:00', new DateTimeZone('GMT+3')),
                ),
            ],
            'timestamp' => [1711705158, new Date(min: 1711705100)],
            'zero-time' => [
                '2021-01-01',
                new Date(format: 'php:Y-m-d', max: new DateTimeImmutable('2021-01-01, 00:00:00')),
            ],
            'rule-locale' => [
                '29.03.2024',
                new Date(locale: 'ru'),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $invalidDateMessage = ['' => ['Invalid date value.']];
        return [
            'php-format-invalid' => ['2021.01.01', new Date(format: 'php:Y-m-d'), $invalidDateMessage],
            'intl-format-invalid' => ['2021.01.01', new Date(format: 'yyyy-MM-dd'), $invalidDateMessage],
            'invalid-date' => ['2021.02.30', new Date(format: 'yyyy-MM-dd'), $invalidDateMessage],
            'invalid-value' => [new stdClass(), new Date(), $invalidDateMessage],
            'min' => [
                '2024-03-29',
                new Date(format: 'yyyy-MM-dd', min: '2025-01-01'),
                ['' => ['The value must be no early than 1/1/25.']],
            ],
            'max' => [
                '2024-03-29',
                new Date(format: 'php:Y-m-d', max: '2024-01-01'),
                ['' => ['The value must be no late than 1/1/24.']],
            ],
            'rule-and-handler-locales' => [
                '2024-03-29',
                new Date(format: 'php:Y-m-d', locale: 'ru', max: '2024-01-01'),
                ['' => ['The value must be no late than 01.01.2024.']],
                [DateHandler::class => new DateHandler(locale: 'en')],
            ],
            'handler-locale' => [
                '2024-03-29',
                new Date(format: 'php:Y-m-d', max: '2024-01-01'),
                ['' => ['The value must be no late than 01.01.2024.']],
                [DateHandler::class => new DateHandler(locale: 'ru')],
            ],
            'timestamp' => [
                1711705158,
                new Date(min: 1711705200),
                ['' => ['The value must be no early than 3/29/24.']],
            ],
            'without-message-date-type' => [
                '29*03*2024',
                new Date(format: 'php:d*m*Y', max: '11*11*2023', dateType: null),
                ['' => ['The value must be no late than 11*11*2023.']],
                [DateHandler::class => new DateHandler(messageDateType: null)],
            ],
            'rule-message-format' => [
                '29*03*2024',
                new Date(format: 'php:d*m*Y', max: '11*11*2023', messageFormat: 'php:d=m=Y'),
                ['' => ['The value must be no late than 11=11=2023.']],
                [DateHandler::class => new DateHandler(messageFormat: 'php:d_m_Y')],
            ],
            'handler-message-type' => [
                '3/29/2024',
                new Date(max: '12/11/2019', dateType: IntlDateFormatter::SHORT),
                ['' => ['The value must be no late than Wednesday, December 11, 2019.']],
                [DateHandler::class => new DateHandler(messageDateType: IntlDateFormatter::FULL)],
            ],
            'rule-message-type-override-handler' => [
                '3/29/2024',
                new Date(max: '12/11/2019', messageDateType: IntlDateFormatter::SHORT),
                ['' => ['The value must be no late than 12/11/19.']],
                [DateHandler::class => new DateHandler(messageDateType: IntlDateFormatter::FULL)],
            ],
            'rule-locale-override-handler' => [
                '12.11.2002',
                new Date(max: '10.11.2002', locale: 'ru'),
                ['' => ['The value must be no late than 10.11.2002.']],
                [DateHandler::class => new DateHandler(locale: 'en')],
            ],
        ];
    }

    public function testDifferentRuleInHandlerItems(): array
    {
        $rule = new RuleWithCustomHandler(DateHandler::class);
        $validator = new Validator();

        $this->expectException(UnexpectedRuleException::class);
        $this->expectExceptionMessage(
            'Expected "' . Date::class . '", "' . DateTime::class . '", "' . Time::class . '", but "' . RuleWithCustomHandler::class . '" given.'
        );
        $validator->validate([], $rule);
    }

    public function testInvalidMinValue(): void
    {
        $rule = new Date(format: 'php:Y-m-d', min: '12.11.2023');
        $validator = new Validator();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid date value.');
        $validator->validate('2024-11-01', $rule);
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
