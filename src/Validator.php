<?php


namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
class Validator
{
    /**
     * @var Rules[]
     */
    private array $attributeRules;

    public function validate(DataSetInterface $dataSet): ResultSet
    {
        $results = new ResultSet();
        foreach ($this->attributeRules as $attribute => $rules) {
            $results->addResult($attribute, $rules->validate($dataSet->getAttributeValue($attribute)));
        }
        return $results;
    }

    public function addRule(string $attribute, Rule $rule): void
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules();
        }

        $this->attributeRules[$attribute]->add($rule);
    }

    public function addRules(iterable $rules = []): void
    {
        foreach ($rules as $attribute => $ruleSets) {
            foreach ($ruleSets as $rule) {
                if (is_callable($rule)) {
                    $rule = new Callback($rule);
                }
                $this->addRule($attribute, $rule);
            }
        }
    }
}
