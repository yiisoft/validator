<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class HasLength extends Rule
{
    public function __construct(
        /**
         * @var int|null minimum length. null means no minimum length limit.
         *
         * @see $tooShortMessage for the customized message for a too short string.
         */
        private ?int $min = null,
        /**
         * @var int|null maximum length. null means no maximum length limit.
         *
         * @see $tooLongMessage for the customized message for a too long string.
         */
        private ?int $max = null,
        /**
         * @var string user-defined error message used when the value is not a string.
         */
        private string $message = 'This value must be a string.',
        /**
         * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
         */
        private string $tooShortMessage = 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
        /**
         * @var string user-defined error message used when the length of the value is greater than {@see $max}.
         */
        private string $tooLongMessage = 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
        /**
         * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
         * If this property is not set, application wide encoding will be used.
         */
        protected string $encoding = 'UTF-8',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    /**
     * @see $min
     */
    public function min(?int $value): self
    {
        $new = clone $this;
        $new->min = $value;

        return $new;
    }

    /**
     * @see $max
     */
    public function max(?int $value): self
    {
        $new = clone $this;
        $new->max = $value;

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

    /**
     * @see $tooShortMessage
     */
    public function tooShortMessage(string $value): self
    {
        $new = clone $this;
        $new->tooShortMessage = $value;

        return $new;
    }

    /**
     * @see $tooLongMessage
     */
    public function tooLongMessage(string $value): self
    {
        $new = clone $this;
        $new->tooLongMessage = $value;

        return $new;
    }

    /**
     * @see $encoding
     */
    public function encoding(string $value): self
    {
        $new = clone $this;
        $new->encoding = $value;

        return $new;
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $result->addError($this->formatMessage($this->tooShortMessage, ['min' => $this->min]));
        }
        if ($this->max !== null && $length > $this->max) {
            $result->addError($this->formatMessage($this->tooLongMessage, ['max' => $this->max]));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'max' => $this->max,
            'message' => $this->formatMessage($this->message),
            'tooShortMessage' => $this->formatMessage($this->tooShortMessage, ['min' => $this->min]),
            'tooLongMessage' => $this->formatMessage($this->tooLongMessage, ['max' => $this->max]),
            'encoding' => $this->encoding,
        ]);
    }
}
