<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;
use Yiisoft\I18n\TranslatorInterface;

/**
 * Rules represents multiple rules for a single value
 */
class Rules
{
    /**
     * @var Rule[]
     */
    private array $rules = [];
    private ?TranslatorInterface $translator;
    private ?string $translationDomain;
    private ?string $translationLocale;

    public function __construct(
        iterable $rules = [],
        ?TranslatorInterface $translator = null,
        ?string $translationDomain = null,
        ?string $translationLocale = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->translationLocale = $translationLocale;

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

        if ($this->translator !== null) {
            $rule = $rule->translator($this->translator);
        }

        if ($this->translationDomain !== null) {
            $rule = $rule->translationDomain($this->translationDomain);
        }

        if ($this->translationLocale !== null) {
            $rule = $rule->translationLocale($this->translationLocale);
        }

        return $rule;
    }

    public function add(Rule $rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result
    {
        $compoundResult = new Result();
        /**
         * @var $rule Rule
         */
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
}
