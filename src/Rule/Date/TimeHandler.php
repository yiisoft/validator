<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

/**
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 */
final class TimeHandler extends BaseDateHandler
{
    /**
     * @psalm-param IntlDateFormatterFormat $timeType
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        int $timeType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageTimeType = IntlDateFormatter::SHORT,
        string $incorrectInputMessage = '{Property} must be a time.',
        string $tooEarlyMessage = '{Property} must be no early than {limit}.',
        string $tooLateMessage = '{Property} must be no late than {limit}.',
    ) {
        parent::__construct(
            IntlDateFormatter::NONE,
            $timeType,
            $timeZone,
            $locale,
            $messageFormat,
            IntlDateFormatter::NONE,
            $messageTimeType,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
        );
    }
}
