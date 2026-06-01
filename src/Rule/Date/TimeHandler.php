<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

use function func_num_args;

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
        string $tooEarlyMessage = '{Property} must be no earlier than {limit}.',
        string $tooLateMessage = '{Property} must be no later than {limit}.',
    ) {
        $argumentCount = func_num_args();
        $messageTimeTypeFallbackToRuleType = $messageTimeType === null;

        // Keep the public default value for BC, but treat it as unset when omitted.
        if ($messageTimeType === IntlDateFormatter::SHORT && $argumentCount !== 5) {
            $messageTimeType = null;
            $messageTimeTypeFallbackToRuleType = false;
        }

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
            true,
            $messageTimeTypeFallbackToRuleType,
        );
    }
}
