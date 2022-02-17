<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

final class Subset extends Rule
{
    /**
     * @var iterable
     */
    private iterable $values;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    private bool $strict = false;

    private string $iterableMessage = 'Value must be iterable';

    private string $subsetMessage = 'Values must be ones of {values}.';

    public static function rule(iterable $values): self
    {
        $rule = new self();
        $rule->values = $values;
        return $rule;
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_iterable($value)) {
            $result->addError($this->formatMessage($this->iterableMessage));
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $this->values, $this->strict)) {
            $valuesString = '"' . implode('", "', (is_array($this->values) ? $this->values : iterator_to_array($this->values))) . '"';

            $result->addError($this->formatMessage($this->subsetMessage, [
                'values' => $valuesString,
            ]));
        }

        return $result;
    }

    public function strict(): self
    {
        $new = clone $this;
        $new->strict = true;
        return $new;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'iterableMessage' => $this->formatMessage($this->iterableMessage),
                'subsetMessage' => $this->formatMessage($this->subsetMessage),
                'values' => $this->values,
                'strict' => $this->strict,
            ],
        );
    }
}
