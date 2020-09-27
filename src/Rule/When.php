<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

class When extends Rule
{
    /**
     * @var Rule
     */
    private Rule $rule;

    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback, Rule $rule)
    {
        $this->callback = $callback;
        $this->rule = $rule;
    }

    /**
     * @inheritDoc
     */
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        if (call_user_func($this->callback, $value, $dataSet)) {
            return $this->rule->validate($value, $dataSet);
        }

        return new Result();
    }
}
