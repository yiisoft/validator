<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use Attribute;
use Closure;
use DateTimeInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is a time.
 *
 * An example for simple that can be used to validate the time:
 *
 * ```php
 * $rules = [
 *     'time' => new Yiisoft\Validator\Rule\Date\Time(format: 'php:H:i'),
 * ];
 * ```
 *
 * In the example above, the PHP attributes equivalent will be:
 *
 * ```php
 * use Yiisoft\Validator\Validator;
 * use Yiisoft\Validator\Rule\Date\Date;
 *
 * final class User
 * {
 *     public function __construct(
 *         #[Date(format: 'php:H:i')]
 *         public string $time,
 *     ) {}
 * }
 *
 * $user = new User(time: '12:35');
 *
 * $validator = (new Validator())->validate($user);
 * ```
 *
 * @see TimeHandler
 *
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Time extends BaseDate
{
    /**
     * @param string|null $format The time format that the value being validated should follow. This can be a time
     * pattern as described in the
     * [ICU manual](https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax).
     *
     * Alternatively this can be a string prefixed with `php:` representing a format that can be recognized by
     * the PHP Datetime class. Please refer to
     * {@link https://www.php.net/manual/datetimeimmutable.createfromformat.php} on supported formats.
     *
     * Here are some example values:
     *
     * ```php
     * 'HH:mm' // time in ICU format
     * 'php:H:i' // time in PHP format
     * ```
     *
     * @param int|null $timeType Format of the time determined by one of the `IntlDateFormatter` constants. It used when
     * {@see $format} is not set.
     *
     * @param string|null $timeZone The timezone to use for parsing and formatting date values. This can be any value
     * that may be passed to
     * [date_default_timezone_set()](https://www.php.net/manual/function.date-default-timezone-set.php)> e.g. `UTC`,
     * `Europe/Berlin` or `America/Chicago`. Refer to the
     * [php manual](https://secure.php.net/manual/en/timezones.php) for available timezones.
     *
     * @param string|null $locale Locale to use when formatting or parsing or `null` to use the value specified in the
     * ini setting `intl.default_locale`.
     *
     * @param int|string|DateTimeInterface|null $min Lower limit of the time. Defaults to `null`, meaning no lower
     * limit. This can be a unix timestamp or a string representing a date value or `DateTimeInterface` instance.
     *
     * @param int|string|DateTimeInterface|null $max Upper limit of the time. Defaults to `null`, meaning no upper
     * limit. This can be a unix timestamp or a string representing a date value or `DateTimeInterface` instance.
     *
     * @param string|null $messageFormat Format of time that used in error messages.
     *
     * @param int|null $messageTimeType Format of time determined by one of the `IntlDateFormatter` constants that used
     * in error messages. It used when {@see $messageFormat} is not set.
     *
     * @param string|null $incorrectInputMessage A message used when the validated value is not valid time. You may use
     * the following placeholders in the message:
     *  - `{attribute}`: the translated label of the attribute being validated.
     *
     * @param string|null $tooEarlyMessage A message used when the validated time is less than {@see $min}. You may use
     * the following placeholders in the message:
     *  - `{attribute}`: the translated label of the attribute being validated.
     *  - `{value}`: the validated time.
     *  - `{limit}`: expected minimum time.
     *
     * @param string|null $tooLateMessage A message used when the validated time is more than {@see $max}. You may use
     * the following placeholders in the message:
     *  - `{attribute}`: the translated label of the attribute being validated.
     *  - `{value}`: the validated time.
     *  - `{limit}`: expected maximum time.
     *
     * @param mixed|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     *
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     *
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param IntlDateFormatterFormat|null $timeType
     * @psalm-param IntlDateFormatterFormat|null $messageTimeType
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        ?string $format = null,
        private ?int $timeType = null,
        ?string $timeZone = null,
        ?string $locale = null,
        int|string|DateTimeInterface|null $min = null,
        int|string|DateTimeInterface|null $max = null,
        ?string $messageFormat = null,
        private ?int $messageTimeType = null,
        ?string $incorrectInputMessage = null,
        ?string $tooEarlyMessage = null,
        ?string $tooLateMessage = null,
        mixed $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            $format,
            $timeZone,
            $locale,
            $min,
            $max,
            $messageFormat,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
            $skipOnEmpty,
            $skipOnError,
            $when,
        );
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    public function getTimeType(): ?int
    {
        return $this->timeType;
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    public function getMessageTimeType(): ?int
    {
        return $this->messageTimeType;
    }

    public function getHandler(): string
    {
        return TimeHandler::class;
    }
}
