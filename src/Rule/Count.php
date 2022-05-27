<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Countable;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Count implements ParametrizedRuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var int|null minimum number of items. null means no minimum number limit.
         *
         * @see $tooFewItemsMessage for the customized message for a value with too few items.
         */
        private ?int $min = null,
        /**
         * @var int|null maximum number of items. null means no maximum number limit.
         *
         * @see $tooManyItemsMessage for the customized message for a value wuth too many items.
         */
        private ?int $max = null,
        /**
         * @var int|null exact number of items. null means no strict comparison. Mutually exclusive with {@see $min} and
         * {@see $max}.
         */
        private ?int $exactly = null,
        /**
         * @var string user-defined error message used when the value is neither an array nor implementing
         * {@see \Countable} interface.
         *
         * @see Countable
         */
        private string $message = 'This value must be an array or implement \Countable interface.',
        /**
         * @var string user-defined error message used when the number of items is smaller than {@see $min}.
         */
        private string $tooFewItemsMessage = 'This value must contain at least {min, number} ' .
        '{min, plural, one{item} other{items}}.',
        /**
         * @var string user-defined error message used when the number of items is greater than {@see $max}.
         */
        private string $tooManyItemsMessage = 'This value must contain at most {max, number} ' .
        '{max, plural, one{item} other{items}}.',
        /**
         * @var string user-defined error message used when the number of items does not equal {@see $exactly}.
         */
        private string $notExactlyMessage = 'This value must contain exactly {max, number} ' .
        '{max, plural, one{item} other{items}}.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        if (!$this->min && !$this->max && !$this->exactly) {
            throw new InvalidArgumentException(
                'At least one of these attributes must be specified: $min, $max, $exactly.'
            );
        }

        if ($this->exactly && ($this->min || $this->max)) {
            throw new InvalidArgumentException('$exactly is mutually exclusive with $min and $max.');
        }

        if ($this->min && $this->max && $this->min === $this->max) {
            throw new InvalidArgumentException('Use $exactly instead.');
        }
    }

    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @return int|null
     */
    public function getExactly(): ?int
    {
        return $this->exactly;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTooFewItemsMessage(): string
    {
        return $this->tooFewItemsMessage;
    }

    /**
     * @return string
     */
    public function getTooManyItemsMessage(): string
    {
        return $this->tooManyItemsMessage;
    }

    /**
     * @return string
     */
    public function getNotExactlyMessage(): string
    {
        return $this->notExactlyMessage;
    }

    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }

    #[ArrayShape([
        'min' => 'int|null',
        'max' => 'int|null',
        'exactly' => 'int|null',
        'message' => 'string[]',
        'tooFewItemsMessage' => 'array',
        'tooManyItemsMessage' => 'array',
        'notExactlyMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'exactly' => $this->exactly,
            'message' => [
                'message' => $this->message,
            ],
            'tooFewItemsMessage' => [
                'message' => $this->tooFewItemsMessage,
                'parameters' => ['min' => $this->min],
            ],
            'tooManyItemsMessage' => [
                'message' => $this->tooManyItemsMessage,
                'parameters' => ['max' => $this->max],
            ],
            'notExactlyMessage' => [
                'message' => $this->notExactlyMessage,
                'parameters' => ['exactly' => $this->exactly],
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
