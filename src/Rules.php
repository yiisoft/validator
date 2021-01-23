<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

/**
 * Rules represents multiple rules for a single value
 */
final class Rules
{
    private ?FormatterInterface $formatter = null;

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
        $rule = $this->normalizeRule($rule);
        if ($this->formatter !== null && $rule instanceof FormattableRuleInterface) {
            $rule = $rule->formatter($this->formatter);
        }
        $this->rules[] = $rule;
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

    private function normalizeRule($rule): RuleInterface
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Rule should be either instance of %s or a callable, %s given.',
                RuleInterface::class,
                gettype($rule)
            ));
        }

        return $rule;
    }

    public function withFormatter(FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        $new->addFormatterToRules($formatter);
        return $new;
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

    private function addFormatterToRules(FormatterInterface $formatter): void
    {
        foreach ($this->rules as &$rule) {
            if ($rule instanceof FormattableRuleInterface) {
                $rule = $rule->formatter($formatter);
            }
        }
    }
}
