<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

class Callback extends Rule
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        $callback = $this->callback;
        /**
         * @var $callbackResult Result
         */
        $callbackResult = $callback($value, $dataSet);
        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->translateMessage($message));
            }
        }
        return $result;
    }
}
