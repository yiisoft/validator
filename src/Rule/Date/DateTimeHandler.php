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
    ) {
        parent::__construct($timeZone, $locale, $messageFormat, $messageDateType, $messageTimeType);
    }
}
