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
     * @var RuleInterface[]
     */
    private array $rules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->add($rule);
        }
    }

    /**
     * @param callable|RuleInterface $rule
     */
    public function add($rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, ValidationContext $context = null): Result
    {
        $context = $context ?? new ValidationContext();

        $compoundResult = new Result();
        foreach ($this->rules as $rule) {
            $ruleResult = $rule->validate($value, $context);
            if ($ruleResult->isValid() === false) {
                if (!$context->isPreviousRulesErrored()) {
                    $context = $context->withPreviousRulesErrored(true);
                }
                foreach ($ruleResult->getErrors() as $message) {
                    $compoundResult->addError($message);
                }
            }
        }
        return $compoundResult;
    }

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

        return $rule;
    }

    /**
     * Return rules as array.
     *
     * @return array
     */
    public function asArray(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof ParametrizedRuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            }
        }
        return $arrayOfRules;
    }
}
