<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    /**
     * @var Rules[]
     */
    private array $attributeRules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $attribute => $ruleSets) {
            foreach ($ruleSets as $rule) {
                $this->addRule($attribute, $rule);
            }
        }
    }

    public function validate(DataSetInterface $dataSet): ResultSet
    {
        $results = new ResultSet();
        foreach ($this->attributeRules as $attribute => $rules) {
            $results->addResult(
                $attribute,
                $rules->validate($dataSet->getAttributeValue($attribute), $dataSet)
            );
        }
        return $results;
    }

    /**
     * @param string $attribute
     * @param Rule|callable
     */
    public function addRule(string $attribute, $rule): void
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules([]);
        }
        $this->attributeRules[$attribute]->add($rule);
    }
}
