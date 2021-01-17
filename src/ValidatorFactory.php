<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

final class ValidatorFactory implements ValidatorFactoryInterface
{
    public function __construct()
    {
    }

    public function create(array $rules): ValidatorInterface
    {
        return new Validator($this->normalizeRules($rules));
    }

    private function normalizeRules(array $rules)
    {
        foreach ($rules as $attribute => $ruleSets) {
            foreach ($ruleSets as $index => $rule) {
                $ruleSets[$index] = $this->normalizeRule($rule);
            }
            $rules[$attribute] = $ruleSets;
        }
        return $rules;
    }

    /**
     * @param callable|Rule $rule
     */
    private function normalizeRule($rule): Rule
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof Rule) {
            throw new \InvalidArgumentException(
                'Rule should be either instance of Rule class or a callable'
            );
        }

        return $rule;
    }
}
