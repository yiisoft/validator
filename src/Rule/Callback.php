<?php


namespace Yiisoft\Validator\Rule;


use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

class Callback extends Rule
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function validateValue($value): Result
    {
        $callback = $this->callback;
        return $callback($value);
    }
}
