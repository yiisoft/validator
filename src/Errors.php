<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * ResultSet stores validation result of each attribute from {@link DataSetInterface}.
 * It is typically obtained by validating data set with {@link Validator}.
 */
final class Errors implements \IteratorAggregate, TranslatableErrorInterface
{
    use TranslatableTrait;

    /**
     * @var Error[]
     */
    private array $results = [];

    public function addResult(
        string $attribute,
        Error $error
    ): void
    {
        if (!isset($this->results[$attribute])) {
            $this->results[$attribute] = $error;
            return;
        }
        if ($error->isValid()) {
            return;
        }
        foreach ($error->getRawErrors() as $errorItem) {
            [$message, $parameters] = $errorItem;
            $this->results[$attribute]->addError($message, $parameters);
        }
    }

    public function getResult(string $attribute): Error
    {
        if (!isset($this->results[$attribute])) {
            throw new \InvalidArgumentException("There is no result for attribute \"$attribute\"");
        }

        $resultError = $this->mutateResult(new Error());
        foreach ($this->results[$attribute]->getRawErrors() as $errorItem) {
            [$message, $parameters] = $errorItem;
            $resultError->addError($message, $parameters);
        }


        return $resultError;
    }

    /**
     * @return \ArrayIterator|\Traversable|Error[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }

    private function mutateResult(Error $e)
    {
        if ($this->translator !== null) {
            $e = $e->translator($this->translator);
        }

        if ($this->translationDomain !== null) {
            $e = $e->translationDomain($this->translationDomain);
        }

        if ($this->translationLocale !== null) {
            $e = $e->translationLocale($this->translationLocale);
        }

        return $e;
    }

}
