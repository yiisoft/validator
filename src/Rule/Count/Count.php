<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Count;

use Attribute;
use Closure;
use Countable;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Count implements RuleInterface
{
    use RuleNameTrait;
    use HandlerClassNameTrait;

    public function __construct(
        /**
         * @var int|null minimum number of items. null means no minimum number limit.
         *
         * @see $tooFewItemsMessage for the customized message for a value with too few items.
         */
        public ?int $min = null,
        /**
         * @var int|null maximum number of items. null means no maximum number limit.
         *
         * @see $tooManyItemsMessage for the customized message for a value wuth too many items.
         */
        public ?int $max = null,
        /**
         * @var int|null exact number of items. null means no strict comparison. Mutually exclusive with {@see $min} and
         * {@see $max}.
         */
        public ?int $exactly = null,
        /**
         * @var string user-defined error message used when the value is neither an array nor implementing
         * {@see \Countable} interface.
         *
         * @see Countable
         */
        public string $message = 'This value must be an array or implement \Countable interface.',
        /**
         * @var string user-defined error message used when the number of items is smaller than {@see $min}.
         */
        public string $tooFewItemsMessage = 'This value must contain at least {min, number} ' .
        '{min, plural, one{item} other{items}}.',
        /**
         * @var string user-defined error message used when the number of items is greater than {@see $max}.
         */
        public string $tooManyItemsMessage = 'This value must contain at most {max, number} ' .
        '{max, plural, one{item} other{items}}.',
        /**
         * @var string user-defined error message used when the number of items does not equal {@see $exactly}.
         */
        public string $notExactlyMessage = 'This value must contain exactly {max, number} ' .
        '{max, plural, one{item} other{items}}.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
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
