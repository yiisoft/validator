<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\CallbackRuleException;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Error;
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

    protected function validateValue($value, DataSetInterface $dataSet = null): Error
    {
        $callback = $this->callback;
        $callbackResult = $callback($value, $dataSet);

        if (!$callbackResult instanceof Error) {
            throw new CallbackRuleException($callbackResult);
        }

        $result = new Error();

        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($message);
            }
        }
        return $result;
    }

    public function getName(): string
    {
        return 'callback';
    }
}
