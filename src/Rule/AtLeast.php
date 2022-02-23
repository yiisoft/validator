<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * AtLeastValidator checks if at least $min of many attributes are filled.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class AtLeast extends Rule
{
    public function __construct(
        /**
         * The list of required attributes that will be checked.
         */
        private array $attributes,
        /**
         * The minimum required quantity of filled attributes to pass the validation.
         * Defaults to 1.
         */
        private int $min = 1,
        /**
         * Message to display in case of error.
         */
        private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $filledCount = 0;

        foreach ($this->attributes as $attribute) {
            if (!$this->isEmpty($value->{$attribute})) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $this->min) {
            $message = $this->formatMessage($this->message, ['min' => $this->min]);
            $result->addError($message);
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'message' => $this->formatMessage($this->message, ['min' => $this->min]),
        ]);
    }
}
