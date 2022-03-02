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
 * RegularExpressionValidator validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the validator will ensure the value do NOT match the pattern.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Regex extends Rule
{
    public function __construct(
        /**
         * @var string the regular expression to be matched with
         */
        private string $pattern,
        /**
         * @var bool whether to invert the validation logic. Defaults to `false`. If set to `true`, the regular
         * expression defined via {@see $pattern} should NOT match the value.
         */
        private bool $not = false,
        private string $incorrectInputMessage = 'Value should be string.',
        private string $message = 'Value is invalid.',
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

        if (!is_string($value)) {
            $result->addError($this->formatMessage($this->incorrectInputMessage));

            return $result;
        }

        if (
            (!$this->not && !preg_match($this->pattern, $value)) ||
            ($this->not && preg_match($this->pattern, $value))
        ) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'pattern' => $this->pattern,
            'not' => $this->not,
            'incorrectInputMessage' => $this->formatMessage($this->incorrectInputMessage),
            'message' => $this->formatMessage($this->message),
        ]);
    }
}
