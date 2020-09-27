<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Error;
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
    protected function validateValue($value, DataSetInterface $dataSet = null): Error
    {
        if (call_user_func($this->callback, $value, $dataSet)) {
            return $this->rule->validate($value, $dataSet);
        }

        return new Error();
    }
}
