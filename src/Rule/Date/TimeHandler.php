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
     * @psalm-param IntlDateFormatterFormat $defaultMessageTimeType
     */
    public function __construct(
        int $timeType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageTimeType = null,
        string $incorrectInputMessage = '{Property} must be a time.',
        string $tooEarlyMessage = '{Property} must be no earlier than {limit}.',
        string $tooLateMessage = '{Property} must be no later than {limit}.',
        int $defaultMessageTimeType = IntlDateFormatter::SHORT,
    ) {
        parent::__construct(
            IntlDateFormatter::NONE,
            $timeType,
            $timeZone,
            $locale,
            $messageFormat,
            null,
            $messageTimeType,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
            null,
            $defaultMessageTimeType,
        );
    }
}
