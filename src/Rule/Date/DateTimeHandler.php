<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

/**
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 */
final class DateTimeHandler extends BaseDateHandler
{
    /**
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param IntlDateFormatterFormat $timeType
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        int $dateType = IntlDateFormatter::SHORT,
        int $timeType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageDateType = IntlDateFormatter::SHORT,
        ?int $messageTimeType = IntlDateFormatter::SHORT,
        string $incorrectInputMessage = 'Invalid date value.',
        string $tooEarlyMessage = '{Property} must be no early than {limit}.',
        string $tooLateMessage = '{Property} must be no late than {limit}.',
    ) {
        parent::__construct(
            $dateType,
            $timeType,
            $timeZone,
            $locale,
            $messageFormat,
            $messageDateType,
            $messageTimeType,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
        );
    }
}
