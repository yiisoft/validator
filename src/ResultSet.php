<?php

namespace Yiisoft\Validator;

final class ResultSet implements \IteratorAggregate
{
    /**
     * @var Result[]
     */
    private $results = [];

    public function addResult(string $attribute, Result $result): void
    {
        if (isset($this->results[$attribute]) && !$result->isValid()) {
            $errors = $result->getErrors();
            $result = $this->results[$attribute];
            foreach ($errors as $error) {
                $result->addError($error);
            }
        }

        $this->results[$attribute] = $result;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }

    public function getResult(string $attribute): Result
    {
        if (!isset($this->results[$attribute])) {
            throw new \InvalidArgumentException("There is no results for attribute \"$attribute\"");
        }

        return $this->results[$attribute];
    }
}
