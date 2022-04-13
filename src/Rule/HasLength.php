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
        private ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result($this->formatter);

        if (!is_string($value)) {
            $result->addError($this->message);
            return $result;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $result->addError($this->tooShortMessage, parameters: ['min' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $result->addError($this->tooLongMessage, parameters: ['max' => $this->max]);
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'min' => $this->min,
            'max' => $this->max,
            'message' => $this->message,
            'tooShortMessage' => [$this->tooShortMessage, ['min' => $this->min]],
            'tooLongMessage' => [$this->tooLongMessage, ['max' => $this->max]],
            'encoding' => $this->encoding,
        ]);
    }
}
