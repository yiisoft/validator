<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

/**
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 */
final class DateHandler extends BaseDateHandler
{
    /**
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param IntlDateFormatterFormat $defaultMessageDateType
     */
    public function __construct(
        int $dateType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageDateType = null,
        string $incorrectInputMessage = '{Property} must be a date.',
        string $tooEarlyMessage = '{Property} must be no earlier than {limit}.',
        string $tooLateMessage = '{Property} must be no later than {limit}.',
        int $defaultMessageDateType = IntlDateFormatter::SHORT,
    ) {
        parent::__construct(
            $dateType,
            IntlDateFormatter::NONE,
            $timeZone,
            $locale,
            $messageFormat,
            $messageDateType,
            null,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
            $defaultMessageDateType,
            null,
        );
    }
}
