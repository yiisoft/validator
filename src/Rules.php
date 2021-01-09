<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Callback;

/**
 * Rules represents multiple rules for a single value
 */
final class Rules
{
    private ?TranslatorInterface $translator = null;

    /**
     * @var Rule[] $rules
     */
    private array $rules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->add($rule);
        }
    }

    /**
     * @param callable|Rule $rule
     */
    public function add($rule): void
    {
        $rule = $this->normalizeRule($rule);
        if ($this->translator !== null) {
            $rule = $rule->translator($this->translator);
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

    private function normalizeRule($rule): Rule
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof Rule) {
            throw new \InvalidArgumentException(sprintf(
                'Rule should be either instance of Rule class or a callable, %s given.',
                gettype($rule)
            ));
        }

        return $rule;
    }

    public function withTranslator(TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;
        $new->addTranslatorToRules($translator);
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
            $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
        }
        return $arrayOfRules;
    }

    private function addTranslatorToRules(TranslatorInterface $translator): void
    {
        foreach ($this->rules as &$rule) {
            $rule = $rule->translator($translator);
        }
    }
}
