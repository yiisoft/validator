<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

final class ValidatorFactory implements ValidatorFactoryInterface
{
    private ?FormatterInterface $formatter;

    public function __construct(
        FormatterInterface $formatter = null
    ) {
        $this->formatter = $formatter;
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
     * @param callable|RuleInterface $rule
     */
    private function normalizeRule($rule): RuleInterface
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Rule should be either instance of %s or a callable',
                RuleInterface::class
            ));
        }

        if ($this->formatter !== null && $rule instanceof FormatableRuleInterface) {
            $rule = $rule->formatter($this->formatter);
        }

        return $rule;
    }
}
