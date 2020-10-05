<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

/**
 * Rules represents multiple rules for a single value
 */
final class Rules
{
    /**
     * @var AbstractRule[]
     */
    private array $rules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->add($rule);
        }
    }

    /**
     * @param AbstractRule|callable
     */
    public function add($rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result
    {
        $compoundResult = new Result();
        foreach ($this->rules as $rule) {
            $ruleResult = $rule->validate($value, $dataSet, $previousRulesErrored);
            if ($ruleResult->isValid() === false) {
                $previousRulesErrored = true;
                foreach ($ruleResult->getErrors() as $message) {
                    $compoundResult->addError($message);
                }
            }
        }
        return $compoundResult;
    }

    private function normalizeRule($rule): AbstractRule
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof AbstractRule) {
            throw new \InvalidArgumentException('Rule should be either instance of Rule class or a callable');
        }

        return $rule;
    }

    /**
     * Return rules as array.
     * @return array
     */
    public function asArray(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
        }
        return $arrayOfRules;
    }
}
