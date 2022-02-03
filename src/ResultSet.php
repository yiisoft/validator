<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use ArrayIterator;
use Closure;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * ResultSet stores validation result of each attribute from {@link DataSetInterface}.
 * It is typically obtained by validating data set with {@link Validator}.
 */
final class ResultSet implements IteratorAggregate, ErrorsReadInterface
{
    /**
     * @var Result[]
     * @psalm-var array<string, Result>
     */
    private array $results = [];
    private bool $isValid = true;

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->results);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @psalm-return array<string, Error>
     */
    public function getErrorObjects(): array
    {
        return $this->getAttributeToErrorsMap(static fn (Result $result) => $result->getErrorObjects());
    }

    /**
     * @return string[][]
     * @psalm-return array<string, array<int|string, string>>
     */
    public function getErrors(): array
    {
        return $this->getAttributeToErrorsMap(static fn (Result $result) => $result->getErrors());
    }

    public function getNestedErrors(): array
    {
        return $this->getAttributeToErrorsMap(static fn (Result $result) => $result->getNestedErrors());
    }

    public function getErrorsIndexedByPath(string $separator = '.'): array
    {
        return $this->getAttributeToErrorsMap(static fn (Result $result) => $result->getErrorsIndexedByPath($separator));
    }

    private function getAttributeToErrorsMap(Closure $getErrorsClosure): array
    {
        $errors = [];
        foreach ($this->results as $attribute => $result) {
            if (!$result->isValid()) {
                $errors[$attribute] = $getErrorsClosure($result);
            }
        }

        return $errors;
    }

    public function addResult(string $attribute, Result $result): void
    {
        if (!$result->isValid()) {
            $this->isValid = false;
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

    public function getResult(string $attribute): Result
    {
        if (!isset($this->results[$attribute])) {
            throw new InvalidArgumentException("There is no result for attribute \"$attribute\"");
        }

        return $this->results[$attribute];
    }
}
