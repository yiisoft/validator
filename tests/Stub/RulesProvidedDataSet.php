<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\RulesProviderInterface;

final class RulesProvidedDataSet implements RulesProviderInterface
{
    private array $data;
    private array $rules;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function getAttributeValue(string $attribute)
    {
        if (!isset($this->data[$attribute])) {
            throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
        }

        return $this->data[$attribute];
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
