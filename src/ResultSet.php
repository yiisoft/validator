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
        if(!isset($this->results[$attribute])) {
            $this->results[$attribute] = $result;
            return;
        }
        if($result->isValid()) {
            return;
        }
        foreach ($result->getErrors() as $error) {
            $this->results[$attribute]->addError($error);
        }
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
