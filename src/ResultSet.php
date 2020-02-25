<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;

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

    public function addResult(
        string $attribute,
        Result $result,
        ?TranslatorInterface $translator = null,
        ?string $translationDomain = null,
        ?string $translationLocale = null
    ): void {
        if (!isset($this->results[$attribute])) {
            $this->results[$attribute] = $result;
            return;
        }
        if ($result->isValid()) {
            return;
        }
        foreach ($result->getErrors($translator, $translationDomain, $translationLocale) as $error) {
            $this->results[$attribute]->addError($error);
        }
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
