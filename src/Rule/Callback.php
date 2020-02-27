<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

class Callback extends Rule
{
    private $callback;

    public function __construct($callback)
    {
        if (!(is_callable($callback) || (is_array($callback) && count($callback) === 2))) {
            throw new \InvalidArgumentException(
                'The argument must be a callback or an array with the class and method name.'
            );
        }

        $this->callback = $callback;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        $callback = $this->callback;

        /**
         * @var $callbackResult Result
         */
        $callbackResult = is_callable($callback)
            ? $callback($value, $dataSet)
            : $this->invokeMethod($value, $callback);

        if ($callbackResult->isValid() === false) {
            foreach ($callbackResult->getErrors() as $message) {
                $result->addError($this->translateMessage($message));
            }
        }
        return $result;
    }

    private function invokeMethod($value, array $callback): Result
    {
        [$class, $method] = $callback;
        if (!method_exists($class, $method)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Method "%s" targeted by Callback rule does not exist in class %s',
                    $method,
                    \get_class($class)
                )
            );
        }
        $reflMethod = new \ReflectionMethod($class, $method);
        if (is_string($class) && !$reflMethod->isStatic()) {
            throw new \InvalidArgumentException(
                sprintf('Method "%s" targeted by Callback rule must be static.', $method)
            );
        }
        if (!$reflMethod->isPublic()) {
            $reflMethod->setAccessible(true);
        }
        if ($reflMethod->isStatic()) {
            return $reflMethod->invoke(null, $value);
        }
        return $reflMethod->invoke($class, $value);
    }
}
