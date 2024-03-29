<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use Attribute;
use Closure;
use DateTimeInterface;
use IntlDateFormatter;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * @psalm-import-type IntlDateFormatterFormat from BaseDate
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Time extends BaseDate
{
    /**
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param IntlDateFormatterFormat|null $timeType
     * @psalm-param IntlDateFormatterFormat|null $messageTimeType
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        ?string $format = null,
        private ?int $timeType = null,
        ?string $timeZone = null,
        ?string $locale = null,
        int|string|DateTimeInterface|null $min = null,
        int|string|DateTimeInterface|null $max = null,
        ?string $messageFormat = null,
        private ?int $messageTimeType = null,
        ?string $incorrectInputMessage = null,
        ?string $tooEarlyMessage = null,
        ?string $tooLateMessage = null,
        mixed $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            $format,
            $timeZone,
            $locale,
            $min,
            $max,
            $messageFormat,
            $incorrectInputMessage,
            $tooEarlyMessage,
            $tooLateMessage,
            $skipOnEmpty,
            $skipOnError,
            $when,
        );
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    public function getTimeType(): ?int
    {
        return $this->timeType;
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    public function getMessageTimeType(): ?int
    {
        return $this->messageTimeType;
    }

    public function getHandler(): string
    {
        return TimeHandler::class;
    }
}
