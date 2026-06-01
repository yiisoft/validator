<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

use function func_num_args;

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
        string $incorrectInputMessage = '{Property} must be a date.',
        string $tooEarlyMessage = '{Property} must be no earlier than {limit}.',
        string $tooLateMessage = '{Property} must be no later than {limit}.',
    ) {
        $argumentCount = func_num_args();
        $messageDateTypeFallbackToRuleType = $messageDateType === null;
        $messageTimeTypeFallbackToRuleType = $messageTimeType === null;

        // Keep the public default values for BC, but treat them as unset when omitted.
        if ($messageDateType === IntlDateFormatter::SHORT && $argumentCount !== 6) {
            $messageDateType = null;
            $messageDateTypeFallbackToRuleType = false;
        }
        if ($messageTimeType === IntlDateFormatter::SHORT && $argumentCount !== 7) {
            $messageTimeType = null;
            $messageTimeTypeFallbackToRuleType = false;
        }

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
            $messageDateTypeFallbackToRuleType,
            $messageTimeTypeFallbackToRuleType,
        );
    }
}
