<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use Attribute;
use Closure;
use DateTimeInterface;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Date\DateHandler;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * @see DateHandler
 *
 * @psalm-type IntlDateFormatterFormat = IntlDateFormatter::FULL | IntlDateFormatter::LONG | IntlDateFormatter::MEDIUM | IntlDateFormatter::SHORT | IntlDateFormatter::NONE
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Date implements RuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param IntlDateFormatterFormat|null $dateType
     * @psalm-param IntlDateFormatterFormat|null $timeType
     * @psalm-param IntlDateFormatterFormat|null $messageDateType
     * @psalm-param IntlDateFormatterFormat|null $messageTimeType
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private ?string $format = null,
        private ?int $dateType = null,
        private ?int $timeType = null,
        private ?string $timeZone = null,
        private ?string $locale = null,
        private int|string|DateTimeInterface|null $min = null,
        private int|string|DateTimeInterface|null $max = null,
        private ?string $messageFormat = null,
        private ?int $messageDateType = null,
        private ?int $messageTimeType = null,
        private string $incorrectInputMessage = 'Invalid date value.',
        private string $tooEarlyMessage = 'The value must be no early than {limit}.',
        private string $tooLateMessage = 'The value must be no late than {limit}.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getFormat(): ?string
    {
        return $this->format;
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
    public function getTimeType(): ?int
    {
        return $this->timeType;
    }

    /**
     * @return non-empty-string|null
     */
    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getName(): string
    {
        return 'date';
    }

    public function getMin(): DateTimeInterface|int|string|null
    {
        return $this->min;
    }

    public function getMax(): DateTimeInterface|int|string|null
    {
        return $this->max;
    }

    public function getMessageFormat(): ?string
    {
        return $this->messageFormat;
    }

    public function getMessageDateType(): ?int
    {
        return $this->messageDateType;
    }

    public function getMessageTimeType(): ?int
    {
        return $this->messageTimeType;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getTooEarlyMessage(): string
    {
        return $this->tooEarlyMessage;
    }

    public function getTooLateMessage(): string
    {
        return $this->tooLateMessage;
    }

    public function getHandler(): string
    {
        return DateHandler::class;
    }
}
