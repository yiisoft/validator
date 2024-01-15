<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Attribute;
use DateTimeImmutable;
use DateTimeInterface;
use Yiisoft\Validator\WhenInterface;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;

/**
 * @see DateTimeHandler
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class DateTime implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var string[]
     */
    private array $formats = [];
    private DateTimeInterface|false|null $min;
    private DateTimeInterface|false|null $max;

    /**
     * Constructor for the class.
     *
     * @param int|DateTimeInterface|null $min The minimum value allowed.
     * @param int|DateTimeInterface|null $max The maximum value allowed.
     * @param string $message The validation error message for invalid DateTime value.
     * @param string $lessThanMinMessage The validation error message for values less than the minimum.
     * @param string $greaterThanMaxMessage The validation error message for values greater than the maximum.
     * @param mixed $skipOnEmpty Determines if the validation should be skipped when the value is empty.
     * @param bool $skipOnError Determines if the validation should be skipped when there is an error.
     * @param Closure|null $when The condition that determines when the validation should be applied.
     * @param string ...$formats The allowed date formats.
     */
    public function __construct(
        int|DateTimeInterface|null $min = null,
        int|DateTimeInterface|null $max = null,
        private string $message = '{attribute} value is not a valid DateTime.',
        private string $lessThanMinMessage = '{attribute} must be no less than {min}.',
        private string $greaterThanMaxMessage = '{attribute} must be no greater than {max}.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
        string ...$formats
    ) {
        $this->formats = $formats;
        $this->min = is_int($min) ? DateTimeImmutable::createFromFormat('U', (string) $min) : $min;
        $this->max = is_int($max) ? DateTimeImmutable::createFromFormat('U', (string) $max) : $max;
    }

    /**
     * @return string[]
     */
    public function getFormats(): array
    {
        if ($this->formats) {
            return $this->formats;
        }

        return [
            DateTimeInterface::ATOM,
            DateTimeInterface::COOKIE,
            DateTimeInterface::RFC822,
            DateTimeInterface::RFC850,
            DateTimeInterface::RFC1036,
            DateTimeInterface::RFC1123,
            DateTimeInterface::RFC7231,
            DateTimeInterface::RFC2822,
            DateTimeInterface::RFC3339,
            DateTimeInterface::RFC3339_EXTENDED,
            DateTimeInterface::RSS,
            DateTimeInterface::W3C,
        ];
    }


    public function getLessThanMinMessage(): string
    {
        return $this->lessThanMinMessage;
    }

    public function getGreaterThanMaxMessage(): string
    {
        return $this->greaterThanMaxMessage;
    }

    public function getMin(): DateTimeInterface|false|null
    {
        return $this->min;
    }

    public function getMax(): DateTimeInterface|false|null
    {
        return $this->max;
    }


    public function getOptions(): array
    {
        return [
            'formats' => $this->formats,
            'min' => $this->min,
            'max' => $this->max,
            'lessThanMinMessage' => [
                'template' => $this->lessThanMinMessage,
                'parameters' => [],
            ],
            'greaterThanMaxMessage' => [
                'template' => $this->greaterThanMaxMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getHandler(): string|RuleHandlerInterface
    {
        return DateTimeHandler::class;
    }
}
