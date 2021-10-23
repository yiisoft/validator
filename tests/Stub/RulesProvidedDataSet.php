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

    public function getRawAttributeValue(string $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
        }

        return $this->data[$attribute];
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->data[$attribute]);
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }
}
