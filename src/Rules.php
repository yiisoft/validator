<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Rule\Callback;
use function is_callable;

/**
 * Rules represents multiple rules for a single value.
 */
final class Rules
{
    public const PARAMETER_PREVIOUS_RULES_ERRORED = 'previousRulesErrored';

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
            $rule = $rule->withFormatter($this->formatter);
        }
        $this->rules[] = $rule;
    }

    public function validate($value, ValidationContext $context = null): Result
    {
        $context = $context ?? new ValidationContext();
        $compoundResult = new Result();

        foreach ($this->rules as $rule) {
            $ruleResult = $rule->validate($value, $context);
            if ($ruleResult->isValid()) {
                continue;
            }

            $context->setParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED, true);

            foreach ($ruleResult->getErrors() as $key => $message) {
                $compoundResult->addError($message, $key);
            }
        }

        return $compoundResult;
    }

    private function normalizeRule($rule): RuleInterface
    {
        if (is_callable($rule)) {
            $rule = Callback::rule($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new InvalidArgumentException(sprintf(
                'Rule should be either an instance of %s or a callable, %s given.',
                RuleInterface::class,
                gettype($rule)
            ));
        }

        return $rule;
    }

    public function withFormatter(?FormatterInterface $formatter): self
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

    private function addFormatterToRules(?FormatterInterface $formatter): void
    {
        foreach ($this->rules as &$rule) {
            if ($rule instanceof FormattableRuleInterface) {
                $rule = $rule->withFormatter($formatter);
            }
        }
    }
}
