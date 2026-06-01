<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

use function func_num_args;

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
        string $incorrectInputMessage = '{Property} must be a date.',
        string $tooEarlyMessage = '{Property} must be no earlier than {limit}.',
        string $tooLateMessage = '{Property} must be no later than {limit}.',
    ) {
        $argumentCount = func_num_args();
        // Keep the public default value for BC, but treat it as unset when omitted.
        if ($messageDateType === IntlDateFormatter::SHORT && $argumentCount !== 5) {
            $messageDateType = null;
        }

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
        );
    }
}
