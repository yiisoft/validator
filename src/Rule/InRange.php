<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

/**
 * In validates that the attribute value is among a list of values.
 *
 * The range can be specified via the [[range]] property.
 * If the [[not]] property is set true, the validator will ensure the attribute value
 * is NOT among the specified range.
 *
 */
class InRange extends Rule
{
    /**
     * @var array|\Traversable
     */
    private $range;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    private bool $strict = false;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the attribute value should NOT be among the list of values defined via [[range]].
     */
    private bool $not = false;

    private string $message = 'This value is invalid.';

    public function __construct($range)
    {
        if (!is_array($range) && !($range instanceof \Traversable)) {
            throw new \RuntimeException('The "range" property must be set.');
        }

        $this->range = $range;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $in = false;

        if (
            ($value instanceof \Traversable || is_array($value)) &&
            ArrayHelper::isSubset($value, $this->range, $this->strict)
        ) {
            $in = true;
        }

        if (!$in && ArrayHelper::isIn($value, $this->range, $this->strict)) {
            $in = true;
        }

        $result = new Result();

        if ($this->not === $in) {
            $result->addError($this->translateMessage($this->message));
        }

        return $result;
    }

    public function strict(): self
    {
        $this->strict = true;
        return $this;
    }

    public function not(): self
    {
        $this->not = true;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
