<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use IntlDateFormatter;

final class TimeHandler extends BaseDateHandler
{
    /**
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        ?string $timeZone = null,
        ?string $locale = null,
        ?string $messageFormat = null,
        ?int $messageTimeType = IntlDateFormatter::SHORT,
    ) {
        parent::__construct($timeZone, $locale, $messageFormat, IntlDateFormatter::NONE, $messageTimeType);
    }
}
