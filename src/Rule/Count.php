<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Countable;
use InvalidArgumentException;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function count;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Count extends Rule
{
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
        private ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
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

        parent::__construct(skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result($this->formatter);

        if (!is_countable($value)) {
            $result->addError($this->message);

            return $result;
        }

        $count = count($value);

        if ($this->exactly !== null && $count !== $this->exactly) {
            $result->addError($this->notExactlyMessage, parameters: ['exactly' => $this->exactly]);

            return $result;
        }

        if ($this->min !== null && $count < $this->min) {
            $result->addError($this->tooFewItemsMessage, parameters: ['min'=> $this->min]);
        }

        if ($this->max !== null && $count > $this->max) {
            $result->addError($this->tooManyItemsMessage, ['max' => $this->max]);
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'max' => $this->max,
            'exactly' => $this->exactly,
            'message' => $this->message,
            'tooFewItemsMessage' => [$this->tooFewItemsMessage, ['min' => $this->min]],
            'tooManyItemsMessage' => [$this->tooManyItemsMessage, ['max' => $this->max]],
            'notExactlyMessage' => [$this->notExactlyMessage, ['exactly' => $this->exactly]],
        ]);
    }
}
