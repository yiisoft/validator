<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * BooleanValidator checks if the value is a boolean value or a value corresponding to it.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Boolean extends Rule
{
    public function __construct(
        /**
         * @var mixed the value representing true status. Defaults to '1'.
         */
        private $trueValue = '1',
        /**
         * @var mixed the value representing false status. Defaults to '0'.
         */
        private $falseValue = '0',
        /**
         * @var bool whether the comparison to {@see $trueValue} and {@see $falseValue} is strict.
         * When this is `true`, the value and type must both match those of {@see $trueValue} or
         * {@see $falseValue}. Defaults to `false`, meaning only the value needs to be matched.
         */
        private bool $strict = false,
        private string $message = 'The value must be either "{true}" or "{false}".',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        if ($this->strict) {
            $valid = $value === $this->trueValue || $value === $this->falseValue;
        } else {
            $valid = $value == $this->trueValue || $value == $this->falseValue;
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $message = $this->getFormattedMessage();
        $result->addError($message);

        return $result;
    }

    private function getFormattedMessage(): string
    {
        return $this->formatMessage($this->message, [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
            'false' => $this->falseValue === false ? 'false' : $this->falseValue,
        ]);
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'trueValue' => $this->trueValue,
            'falseValue' => $this->falseValue,
            'strict' => $this->strict,
            'message' => $this->getFormattedMessage(),
        ]);
    }
}
