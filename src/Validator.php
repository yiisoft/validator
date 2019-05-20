<?php


namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

class Validator
{
    /**
     * @var Rules[]
     */
    private $attributeRules;

    /**
     * Validator constructor.
     * @param $rules
     */
    public function __construct(array $rules = [])
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

    public function validate(DataSet $dataSet): ResultSet
    {
        $results = new ResultSet();
        foreach ($this->attributeRules as $attribute => $rules)
        {
            $results->addResult($attribute, $rules->validate($dataSet->getValue($attribute)));
        }
        return $results;
    }

    public function addRule(string $attribute, Rule $rule): self
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules();
        }

        $this->attributeRules[$attribute]->add($rule);
        return $this;
    }
}
