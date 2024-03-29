<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use RuntimeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * @see Date
 *
 * @psalm-import-type IntlDateFormatterFormat from Date
 */
final class DateHandler implements RuleHandlerInterface
{
    /**
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        private ?string $timeZone = null,
        private ?string $locale = null,
        private ?string $messageFormat = null,
        private ?int $messageDateType = null,
        private ?int $messageTimeType = null,
    ) {
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Date) {
            throw new UnexpectedRuleException(Date::class, $rule);
        }

        $timeZone = $rule->getTimeZone() ?? $this->timeZone;
        if ($timeZone !== null) {
            $timeZone = new DateTimeZone($timeZone);
        }

        $result = new Result();

        $date = $this->prepareValue($value, $rule, $timeZone);
        if ($date === null) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
            ]);
            return $result;
        }

        $min = $this->prepareValue($rule->getMin(), $rule, $timeZone);
        if ($min !== null && $date < $min) {
            $result->addError($rule->getTooEarlyMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'date' => $this->formatDate($date, $rule, $timeZone),
                'limit' => $this->formatDate($min, $rule, $timeZone),
            ]);
            return $result;
        }

        $max = $this->prepareValue($rule->getMax(), $rule, $timeZone);
        if ($max !== null && $date > $max) {
            $result->addError($rule->getTooLateMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'date' => $this->formatDate($date, $rule, $timeZone),
                'limit' => $this->formatDate($max, $rule, $timeZone),
            ]);
            return $result;
        }

        return $result;
    }

    private function prepareValue(mixed $value, Date $rule, ?DateTimeZone $timeZone): ?DateTimeInterface
    {
        $format = $rule->getFormat();

        if (is_int($value)) {
            return $this->makeDateTimeFromTimestamp($value, $timeZone);
        }

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (!is_string($value) || empty($value)) {
            return null;
        }

        if (is_string($format) && str_starts_with($format, 'php:')) {
            return $this->prepareValueWithPhpFormat($value, substr($format, 4), $timeZone);
        }

        return $this->prepareValueWithIntlFormat(
            $value,
            $format,
            $rule->getDateType(),
            $rule->getTimeType(),
            $timeZone,
            $rule->getLocale() ?? $this->locale,
        );
    }

    /**
     * @psalm-param non-empty-string $value
     */
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
        if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return null;
        }

        // If no time was provided in the format string set time to 0 to get a simple date timestamp.
        if (!strpbrk($format, 'aAghGHisvuU')) {
            $date = $date->setTime(0, 0);
        }

        return $date;
    }

    /**
     * @psalm-param non-empty-string $value
     * @psalm-param IntlDateFormatterFormat|null $dateType
     * @psalm-param IntlDateFormatterFormat|null $timeType
     */
    private function prepareValueWithIntlFormat(
        string $value,
        ?string $format,
        ?int $dateType,
        ?int $timeType,
        ?DateTimeZone $timeZone,
        ?string $locale,
    ): ?DateTimeInterface
    {
        $formatter = $this->makeFormatter($format, $locale, $dateType, $timeType, $timeZone);
        $formatter->setLenient(false);
        $timestamp = $formatter->parse($value);
        return is_int($timestamp) ? $this->makeDateTimeFromTimestamp($timestamp, $timeZone) : null;
    }

    private function formatDate(DateTimeInterface $date, Date $rule, ?DateTimeZone $timeZone): string
    {
        $format = $rule->getMessageFormat() ?? $this->messageFormat ?? $rule->getFormat();
        if (is_string($format) && str_starts_with($format, 'php:')) {
            return $date->format(substr($format, 4));
        }

        $formatter = $this->makeFormatter(
            $format,
            $rule->getLocale() ?? $this->locale,
            $rule->getMessageDateType() ?? $this->messageDateType ?? $rule->getDateType(),
            $rule->getMessageTimeType() ?? $this->messageTimeType ?? $rule->getTimeType(),
            $timeZone,
        );

        return $formatter->format($date);
    }

    private function makeFormatter(
        ?string $format,
        ?string $locale,
        ?int $dateType,
        ?int $timeType,
        ?DateTimeZone $timeZone
    ): IntlDateFormatter {
        if (!extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to parse date.');
        }

        if ($format === null) {
            return new IntlDateFormatter(
                $locale,
                $dateType ?? IntlDateFormatter::NONE,
                $timeType ?? IntlDateFormatter::NONE,
                $timeZone,
            );
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
}
