<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

class Subset extends Rule
{
    /**
     * @var iterable
     */
    private iterable $range;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    private bool $strict = false;

    private string $iterableMessage = 'Value must be iterable';

    private string $subsetMessage = 'Value must be subset of...';

    public function __construct(iterable $range)
    {
        $this->range = $range;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_iterable($value)) {
            $result->addError($this->formatMessage($this->iterableMessage));
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $this->range, $this->strict)) {
            $result->addError($this->formatMessage($this->subsetMessage));
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
                'range' => $this->range,
                'strict' => $this->strict,
            ],
        );
    }
}
