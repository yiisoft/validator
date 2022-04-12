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

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
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
        ?FormatterInterface $formatter = null,
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

        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_countable($value)) {
            $result->addError($this->formatMessage($this->message));

            return $result;
        }

        $count = count($value);

        if ($this->exactly !== null && $count !== $this->exactly) {
            $message = $this->formatMessage($this->notExactlyMessage, ['exactly' => $this->exactly]);
            $result->addError($message);

            return $result;
        }

        if ($this->min !== null && $count < $this->min) {
            $message = $this->formatMessage($this->tooFewItemsMessage, ['min' => $this->min]);
            $result->addError($message);
        }

        if ($this->max !== null && $count > $this->max) {
            $message = $this->formatMessage($this->tooManyItemsMessage, ['max' => $this->max]);
            $result->addError($message);
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'max' => $this->max,
            'exactly' => $this->exactly,
            'message' => $this->formatMessage($this->message),
            'tooFewItemsMessage' => $this->formatMessage($this->tooFewItemsMessage, ['min' => $this->min]),
            'tooManyItemsMessage' => $this->formatMessage($this->tooManyItemsMessage, ['max' => $this->max]),
            'notExactlyMessage' => $this->formatMessage($this->notExactlyMessage, ['exactly' => $this->exactly]),
        ]);
    }
}
