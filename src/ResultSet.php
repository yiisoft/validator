<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * ResultSet stores validation result of each attribute from {@link DataSetInterface}.
 * It is typically obtained by validating data set with {@link Validator}.
 */
final class ResultSet implements IteratorAggregate
{
    /**
     * @var Result[]
     */
    private array $results = [];
    private bool $isValid = true;

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

        foreach ($result->getRawErrors() as $error) {
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

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->results);
    }

    /**
     * @param ErrorMessageFormatterInterface|null $formatter
     *
     * @return ErrorMessage[][]
     */
    public function getRawErrors(): array
    {
        $errors = [];
        foreach ($this->results as $attribute => $result) {
            if (!$result->isValid()) {
                $errors[$attribute] = $result->getRawErrors();
            }
        }

        return $errors;
    }

    /**
     * @param ErrorMessageFormatterInterface|null $formatter
     *
     * @return string[][]
     */
    public function getErrors(?ErrorMessageFormatterInterface $formatter = null): array
    {
        $errors = $this->getRawErrors();
        foreach ($errors as $attribute => $attributeErrors) {
            $errors[$attribute] = array_map(
                function ($error) use ($formatter) {
                    return $error->getFormattedMessage($formatter);
                },
                $attributeErrors
            );
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}
