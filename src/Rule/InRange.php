<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * In validates that the attribute value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the validator will ensure the attribute value
 * is NOT among the specified range.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class InRange extends Rule
{
    public function __construct(
        private iterable $range,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        /**
         * @var bool whether to invert the validation logic. Defaults to false. If set to true,
         * the attribute value should NOT be among the list of values passed via constructor.
         */
        private bool $not = false,
        private string $message = 'This value is invalid.',

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

        if ($this->not === ArrayHelper::isIn($value, $this->range, $this->strict)) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'range' => $this->range,
            'strict' => $this->strict,
            'not' => $this->not,
            'message' => $this->formatMessage($this->message),
        ]);
    }
}
