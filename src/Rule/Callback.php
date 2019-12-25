<?php


namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

class Callback extends Rule
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $callback = $this->callback;
        return $callback($value, $dataSet);
    }
}
