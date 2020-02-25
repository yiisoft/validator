<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

/**
 * Rules represents multiple rules for a single value
 */
class Rules
{
    /**
     * @var Rule[]
     */
    private array $rules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->rules[] = $this->normalizeRule($rule);
        }
    }

    private function normalizeRule($rule): Rule
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof Rule) {
            throw new \InvalidArgumentException('Rule should be either instance of Rule class or a callable');
        }

        return $rule;
    }

    public function add(Rule $rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, DataSetInterface $dataSet = null): RuleResult
    {
        $compoundResult = new RuleResult();
        foreach ($this->rules as $rule) {
            $ruleResult = $rule->validate($value, $dataSet);
            if ($ruleResult->isValid() === false) {
                foreach ($ruleResult->getErrors() as $error) {
                    [$message, $arguments] = $error;
                    $compoundResult->addError($message, $arguments);
                }
            }
        }
        return $compoundResult;
    }
}
