<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

final class DateTimeHandler extends BaseDateHandler
{
    /**
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageDateType = IntlDateFormatter::SHORT,
        ?int $messageTimeType = IntlDateFormatter::SHORT,
        string $incorrectInputMessage = 'Invalid date value.',
        string $tooEarlyMessage = 'The value must be no early than {limit}.',
        string $tooLateMessage = 'The value must be no late than {limit}.',
    ) {
        parent::__construct(
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
