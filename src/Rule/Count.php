<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Countable;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;
use function count;
use function is_array;

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
         * @var string user-defined error message used when the value is neither an array nor implementing "Countable"
         * interface.
         *
         * @see Countable
         */
        private string $message = 'This value must be an array or implement "Countable" interface.',
        /**
         * @var string user-defined error message used when the number of items is smaller than {@see $min}.
         */
        private string $tooFewItemsMessage = 'This value should contain at least {min, number} {min, plural, one{items} other{items}}.',
        /**
         * @var string user-defined error message used when the number of items is greater than {@see $max}.
         */
        private string $tooManyItemsMessage = 'This value should contain at most {max, number} {max, plural, one{item} other{items}}.',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_array($value) && !$value instanceof Countable) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        $count = count($value);

        if ($this->min !== null && $count < $this->min) {
            $result->addError($this->formatMessage($this->tooFewItemsMessage, ['min' => $this->min]));
        }
        if ($this->max !== null && $count > $this->max) {
            $result->addError($this->formatMessage($this->tooManyItemsMessage, ['max' => $this->max]));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'max' => $this->max,
            'message' => $this->formatMessage($this->message),
            'tooFewItemsMessage' => $this->formatMessage($this->tooFewItemsMessage, ['min' => $this->min]),
            'tooManyItemsMessage' => $this->formatMessage($this->tooManyItemsMessage, ['max' => $this->max]),
        ]);
    }
}
