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
final class Date extends BaseDate
{
    /**
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param IntlDateFormatterFormat|null $dateType
     * @psalm-param IntlDateFormatterFormat|null $messageDateType
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        ?string $format = null,
        private ?int $dateType = IntlDateFormatter::SHORT,
        ?string $timeZone = null,
        ?string $locale = null,
        int|string|DateTimeInterface|null $min = null,
        int|string|DateTimeInterface|null $max = null,
        ?string $messageFormat = null,
        private ?int $messageDateType = null,
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
    public function getDateType(): ?int
    {
        return $this->dateType;
    }

    /**
     * @psalm-return IntlDateFormatterFormat|null
     */
    public function getMessageDateType(): ?int
    {
        return $this->messageDateType;
    }

    public function getHandler(): string
    {
        return DateHandler::class;
    }
}
