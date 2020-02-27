<?php

declare(strict_types=1);

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
    private bool $hasErrors = false;

    public function addResult(
        string $attribute,
        Result $result
    ): void {
        if ($this->hasErrors === false && $result->isValid() === false) {
            $this->hasErrors = true;
        }
        if (!isset($this->results[$attribute])) {
            $this->results[$attribute] = $result;
            return;
        }
        if ($result->isValid()) {
            return;
        }
        foreach ($result->getErrors() as $error) {
            $this->results[$attribute]->addError($error);
        }
    }

    public function hasErrors(): bool
    {
        return $this->hasErrors;
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
