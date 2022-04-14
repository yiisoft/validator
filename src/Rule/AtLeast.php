<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if at least {@see AtLeast::$min} of many object attributes are filled.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class AtLeast extends Rule
{
    public function __construct(
        /**
         * @var string[] The list of required attributes that will be checked.
         */
        private array $attributes,
        /**
         * @var int The minimum required quantity of filled attributes to pass the validation.
         */
        private int $min = 1,
        /**
         * @var string Message to display in case of error.
         */
        private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    /**
     * @param string[] $value
     *
     * @see $attributes
     */
    public function attributes(array $value): self
    {
        $new = clone $this;
        $new->attributes = $value;

        return $new;
    }

    /**
     * @see $min
     */
    public function min(int $value): self
    {
        $new = clone $this;
        $new->min = $value;

        return $new;
    }

    /**
     * @see $message
     */
    public function message(string $value): self
    {
        $new = clone $this;
        $new->message = $value;

        return $new;
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
            $message = $this->getFormattedMessage();
            $result->addError($message);
        }

        return $result;
    }

    private function getFormattedMessage(): string
    {
        return $this->formatMessage($this->message, ['min' => $this->min]);
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'attributes' => $this->attributes,
            'min' => $this->min,
            'message' => $this->getFormattedMessage(),
        ]);
    }
}
