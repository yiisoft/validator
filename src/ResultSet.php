<?php

namespace Yiisoft\Validator;

/**
 * ResultSet stores validation result of each attribute from {@link DataSetInterface}.
 * It is typically obtained by validating data set with {@link Validator}.
 */
final class ResultSet implements \IteratorAggregate
{
    /**
     * @var Result[]
     */
    private array $results = [];

    public function addResult(string $attribute, Result $result): self
    {
        $new = clone $this;

        if (!isset($new->results[$attribute])) {
            $new->results[$attribute] = $result;
            return $new;
        }
        if ($result->isValid()) {
            return $new;
        }
        foreach ($result->getErrors() as $error) {
            $new->results[$attribute] = $new->results[$attribute]->addError($error);
        }

        return $new;
    }

    public function getResult(string $attribute): Result
    {
        if (!isset($this->results[$attribute])) {
            throw new \InvalidArgumentException("There is no result for attribute \"$attribute\"");
        }

        return $this->results[$attribute];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }
}
