<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

class Callback extends Rule
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $callback = $this->callback;
        $callbackResult = $callback($value, $dataSet);

        if (!$callbackResult instanceof Result) {
            throw new \InvalidArgumentException(
                sprintf('Callback must be return "%s", %s given.', Result::class, gettype($callbackResult))
            );
        }

        $result = new Result();

        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->translateMessage($message));
            }
        }
        return $result;
    }
}
