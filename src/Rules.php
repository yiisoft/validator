<?php


namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

class Rules implements \IteratorAggregate
{
    /**
     * @var Rule[]
     */
    private $rules = [];

    public function __construct(array $rules = [])
    {
        foreach ($rules as $rule) {
            if (is_callable($rule)) {
                $rule = new Callback($rule);
            }
            $this->add($rule);
        }
    }

    public function add(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->rules);
    }

    public function validate($value): Result
    {
        $compoundResult = new Result();
        foreach ($this->getRules($value) as $rule) {
            $ruleResult = $rule->validateValue($value);
            if ($ruleResult->isValid() === false) {
                foreach ($ruleResult->getErrors() as $error) {
                    $compoundResult->addError($error);
                }
            }
        }
        return $compoundResult;
    }

    protected function getRules($value)
    {
        return $this->rules;
    }
}
