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
     */
    public function __construct(
        int $dateType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageDateType = IntlDateFormatter::SHORT,
        string $incorrectInputMessage = 'Invalid date value.',
        string $tooEarlyMessage = 'The value must be no early than {limit}.',
        string $tooLateMessage = 'The value must be no late than {limit}.',
    ) {
        parent::__construct(
            $dateType,
            IntlDateFormatter::NONE,
            $timeZone,
            $locale,
            $messageFormat,
            $messageDateType,
            IntlDateFormatter::NONE,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
        );
    }
}
