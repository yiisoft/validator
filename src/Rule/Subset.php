<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

final class Subset extends Rule
{
    public function __construct(
        private iterable $values,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        private string $iterableMessage = 'Value must be iterable',
        private string $subsetMessage = 'Values must be ones of {values}.',

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

        if (!is_iterable($value)) {
            $result->addError($this->formatMessage($this->iterableMessage));
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $this->values, $this->strict)) {
            $values = is_array($this->values) ? $this->values : iterator_to_array($this->values);
            $valuesString = '"' . implode('", "', $values) . '"';

            $result->addError($this->formatMessage($this->subsetMessage, ['values' => $valuesString]));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'values' => $this->values,
            'strict' => $this->strict,
            'iterableMessage' => $this->formatMessage($this->iterableMessage),
            'subsetMessage' => $this->formatMessage($this->subsetMessage),
        ]);
    }
}
