<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use LogicException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * @see Date
 * @see DateTime
 * @see Time
 *
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 */
abstract class BaseDateHandler implements RuleHandlerInterface
{
    /**
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param IntlDateFormatterFormat $timeType
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        private int $dateType,
        private int $timeType,
        private ?string $timeZone,
        private ?string $locale,
        private ?string $messageFormat,
        private ?int $messageDateType,
        private ?int $messageTimeType,
        private string $incorrectInputMessage,
        private string $tooEarlyMessage,
        private string $tooLateMessage,
    ) {
    }

    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Date && !$rule instanceof DateTime && !$rule instanceof Time) {
            throw new UnexpectedRuleException([Date::class, DateTime::class, Time::class], $rule);
        }

        $timeZone = $rule->getTimeZone() ?? $this->timeZone;
        if ($timeZone !== null) {
            $timeZone = new DateTimeZone($timeZone);
        }

        $result = new Result();

        $date = $this->prepareValue($value, $rule, $timeZone, false);
        if ($date === null) {
            $result->addError(
                $rule->getIncorrectInputMessage() ?? $this->incorrectInputMessage,
                [
                    'attribute' => $context->getTranslatedProperty(),
                    'Attribute' => $context->getCapitalizedTranslatedProperty(),
                ]
            );
            return $result;
        }

        $min = $this->prepareValue($rule->getMin(), $rule, $timeZone, true);
        if ($min !== null && $date < $min) {
            $result->addError(
                $rule->getTooEarlyMessage() ?? $this->tooEarlyMessage,
                [
                    'attribute' => $context->getTranslatedProperty(),
                    'Attribute' => $context->getCapitalizedTranslatedProperty(),
                    'value' => $this->formatDate($date, $rule, $timeZone),
                    'limit' => $this->formatDate($min, $rule, $timeZone),
                ]
            );
            return $result;
        }

        $max = $this->prepareValue($rule->getMax(), $rule, $timeZone, true);
        if ($max !== null && $date > $max) {
            $result->addError(
                $rule->getTooLateMessage() ?? $this->tooLateMessage,
                [
                    'attribute' => $context->getTranslatedProperty(),
                    'Attribute' => $context->getCapitalizedTranslatedProperty(),
                    'value' => $this->formatDate($date, $rule, $timeZone),
                    'limit' => $this->formatDate($max, $rule, $timeZone),
                ]
            );
            return $result;
        }

        return $result;
    }

    private function prepareValue(
        mixed $value,
        Date|DateTime|Time $rule,
        ?DateTimeZone $timeZone,
        bool $strict
    ): ?DateTimeInterface {
        $format = $rule->getFormat();

        if (is_int($value)) {
            return $this->makeDateTimeFromTimestamp($value, $timeZone);
        }

        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            $result = $value;
        } elseif (is_string($value)) {
            if (is_string($format) && str_starts_with($format, 'php:')) {
                $result = $this->prepareValueWithPhpFormat($value, substr($format, 4), $timeZone);
            } else {
                $result = $this->prepareValueWithIntlFormat(
                    $value,
                    $format,
                    $this->getDateTypeFromRule($rule),
                    $this->getTimeTypeFromRule($rule),
                    $timeZone,
                    $rule->getLocale() ?? $this->locale,
                );
            }
        } else {
            $result = null;
        }

        if ($result !== null) {
            if ($rule instanceof Date) {
                $result = DateTimeImmutable::createFromInterface($result)->setTime(0, 0);
            } elseif ($rule instanceof Time) {
                $result = DateTimeImmutable::createFromInterface($result)->setDate(2024, 3, 29);
            }
        }

        return $result === null && $strict
            ? throw new LogicException('Invalid date value.')
            : $result;
    }

    private function prepareValueWithPhpFormat(
        string $value,
        string $format,
        ?DateTimeZone $timeZone
    ): ?DateTimeInterface {
        $date = DateTimeImmutable::createFromFormat($format, $value, $timeZone);
        if ($date === false) {
            return null;
        }

        $errors = DateTimeImmutable::getLastErrors();
        if ($errors !== false && !empty($errors['warning_count'])) {
            return null;
        }

        return $date;
    }

    /**
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param IntlDateFormatterFormat $timeType
     */
    private function prepareValueWithIntlFormat(
        string $value,
        ?string $format,
        int $dateType,
        int $timeType,
        ?DateTimeZone $timeZone,
        ?string $locale,
    ): ?DateTimeInterface {
        $formatter = $this->makeFormatter($format, $locale, $dateType, $timeType, $timeZone);
        $formatter->setLenient(false);
        $timestamp = $formatter->parse($value);
        return is_int($timestamp) ? $this->makeDateTimeFromTimestamp($timestamp, $timeZone) : null;
    }

    private function formatDate(DateTimeInterface $date, Date|DateTime|Time $rule, ?DateTimeZone $timeZone): string
    {
        $formatterDateType = $this->getMessageDateTypeFromRule($rule)
            ?? $this->messageDateType
            ?? $this->getDateTypeFromRule($rule);
        $formatterTimeType = $this->getMessageTimeTypeFromRule($rule)
            ?? $this->messageTimeType
            ?? $this->getTimeTypeFromRule($rule);

        $format = $rule->getMessageFormat() ?? $this->messageFormat;
        if (is_string($format) && str_starts_with($format, 'php:')) {
            return $date->format(substr($format, 4));
        }

        $formatter = $this->makeFormatter(
            $format,
            $rule->getLocale() ?? $this->locale,
            $formatterDateType,
            $formatterTimeType,
            $timeZone,
        );

        return $formatter->format($date);
    }

    private function makeFormatter(
        ?string $format,
        ?string $locale,
        int $dateType,
        int $timeType,
        ?DateTimeZone $timeZone
    ): IntlDateFormatter {
        if ($format === null) {
            return new IntlDateFormatter($locale, $dateType, $timeType, $timeZone);
        }

        return new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            $timeZone,
            pattern: $format
        );
    }

    private function makeDateTimeFromTimestamp(int $timestamp, ?DateTimeZone $timeZone): DateTimeImmutable
    {
        return (new DateTimeImmutable(timezone: $timeZone))->setTimestamp($timestamp);
    }

    /**
     * @psalm-return IntlDateFormatterFormat
     */
    private function getDateTypeFromRule(Date|DateTime|Time $rule): int
    {
        return $rule instanceof Time
            ? IntlDateFormatter::NONE
            : $rule->getDateType() ?? $this->dateType;
    }

    /**
     * @psalm-return IntlDateFormatterFormat
     */
    private function getTimeTypeFromRule(Date|DateTime|Time $rule): int
    {
        return $rule instanceof Date
            ? IntlDateFormatter::NONE
            : $rule->getTimeType() ?? $this->timeType;
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    private function getMessageDateTypeFromRule(Date|DateTime|Time $rule): ?int
    {
        return $rule instanceof Time ? null : $rule->getMessageDateType();
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    private function getMessageTimeTypeFromRule(Date|DateTime|Time $rule): ?int
    {
        return $rule instanceof Date ? null : $rule->getMessageTimeType();
    }
}
