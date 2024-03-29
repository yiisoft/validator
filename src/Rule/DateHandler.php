<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

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
    ) {
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Date) {
            throw new UnexpectedRuleException(Date::class, $rule);
        }

        $result = new Result();

        $prepareResult = $this->prepareValue($value, $rule);
        if ($prepareResult === null) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
            ]);
            return $result;
        }
        [$timestamp, $date] = $prepareResult;

        $prepareResult = $this->prepareValue($rule->getMin(), $rule);
        if ($prepareResult !== null) {
            [$min, $minAsString] = $prepareResult;
            if ($timestamp < $min) {
                $result->addError($rule->getTooEarlyMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'date' => $date,
                    'limit' => $minAsString,
                ]);
                return $result;
            }
        }

        $prepareResult = $this->prepareValue($rule->getMax(), $rule);
        if ($prepareResult !== null) {
            [$max, $maxAsString] = $prepareResult;
            if ($timestamp > $max) {
                $result->addError($rule->getTooLateMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'date' => $date,
                    'limit' => $maxAsString,
                ]);
                return $result;
            }
        }

        return $result;
    }

    /**
     * @psalm-return array{0:int,1:string}|null
     */
    private function prepareValue(mixed $value, Date $rule): ?array
    {
        $timeZone = $rule->getTimeZone() ?? $this->timeZone;
        if ($timeZone !== null) {
            $timeZone = new DateTimeZone($timeZone);
        }

        $locale = $rule->getLocale() ?? $this->locale;

        $format = $rule->getFormat();
        $isPhpFormat = is_string($format) && str_starts_with($format, 'php:');
        if ($isPhpFormat) {
            $format = substr($format, 4);
        }

        if (is_int($value)) {
            $date = $this->makeDateTimeFromTimestamp($value, $timeZone);
            if ($isPhpFormat) {
                /** @var string $format */
                $dateAsString = $date->format($format);
            } else {
                $dateAsString = $this
                    ->makeFormatter(
                        $format,
                        $locale,
                        $rule->getDateType(),
                        $rule->getTimeType(),
                        $timeZone,
                    )
                    ->format($date);
            }
            return [$value, $dateAsString];
        }

        if ($value instanceof DateTimeInterface) {
            if ($isPhpFormat) {
                /** @var string $format */
                $dateAsString = $value->format($format);
            } else {
                $dateAsString = $this
                    ->makeFormatter(
                        $format,
                        $locale,
                        $rule->getDateType(),
                        $rule->getTimeType(),
                        $timeZone,
                    )
                    ->format($value);
            }
            return [$value->getTimestamp(), $dateAsString];
        }

        if (!is_string($value) || empty($value)) {
            return null;
        }

        if ($isPhpFormat) {
            /** @var string $format */
            return $this->prepareValueWithPhpFormat($value, $format, $timeZone);
        }

        return $this->prepareValueWithIntlFormat(
            $value,
            $format,
            $rule->getDateType(),
            $rule->getTimeType(),
            $timeZone,
            $locale,
        );
    }

    /**
     * @psalm-param non-empty-string $value
     * @psalm-return array{0:int,1:string}|null
     */
    private function prepareValueWithPhpFormat(string $value, string $format, ?DateTimeZone $timeZone): ?array
    {
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

        return [$date->getTimestamp(), $date->format($format)];
    }

    /**
     * @psalm-param non-empty-string $value
     * @psalm-param IntlDateFormatterFormat|null $dateType
     * @psalm-param IntlDateFormatterFormat|null $timeType
     * @psalm-return array{0:int,1:string}|null
     */
    private function prepareValueWithIntlFormat(
        string $value,
        ?string $format,
        ?int $dateType,
        ?int $timeType,
        ?DateTimeZone $timeZone,
        ?string $locale,
    ): ?array {
        $formatter = $this->makeFormatter($format, $locale, $dateType, $timeType, $timeZone);
        $formatter->setLenient(false);
        $timestamp = $formatter->parse($value);
        return is_int($timestamp)
            ? [$timestamp, $formatter->format($this->makeDateTimeFromTimestamp($timestamp, $timeZone))]
            : null;
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
