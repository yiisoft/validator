<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Date;

use Closure;
use DateTimeInterface;
use IntlDateFormatter;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * @see BaseDateHandler
 *
 * @psalm-type IntlDateFormatterFormat = IntlDateFormatter::FULL | IntlDateFormatter::LONG | IntlDateFormatter::MEDIUM | IntlDateFormatter::SHORT | IntlDateFormatter::NONE
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
abstract class BaseDate implements RuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @psalm-param non-empty-string|null $timeZone
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private ?string $format = null,
        private ?string $timeZone = null,
        private ?string $locale = null,
        private int|string|DateTimeInterface|null $min = null,
        private int|string|DateTimeInterface|null $max = null,
        private ?string $messageFormat = null,
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
}
