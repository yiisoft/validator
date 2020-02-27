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
            $rule->withTranslator($this->translator);
        }

        if ($this->translationDomain !== null) {
            $rule->withTranslationDomain($this->translationDomain);
        }

        if ($this->translationLocale !== null) {
            $rule->withTranslationLocale($this->translationLocale);
        }

        return $rule;
    }

    public function add(Rule $rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, DataSetInterface $dataSet = null, ResultSet $resultSet = null): Result
    {
        $compoundResult = new Result();
        /**
         * @var $rule Rule
         */
        foreach ($this->rules as $rule) {
            if ($rule->getSkipOnError() === true && $this->skipValidate($rule, $compoundResult, $resultSet)) {
                continue;
            }
            $ruleResult = $rule->validate($value, $dataSet);
            if ($ruleResult->isValid() === false) {
                foreach ($ruleResult->getErrors() as $message) {
                    $compoundResult->addError($message);
                }
            }
        }
        return $compoundResult;
    }

    private function skipValidate(Rule $rule, Result $result, ResultSet $resultSet = null): bool
    {
        if (
            ($rule->getSkipErrorMode() === Rule::SKIP_ON_ATTRIBUTE_ERROR && $result->isValid() === false) ||
            ($rule->getSkipErrorMode() === Rule::SKIP_ON_ANY_ERROR && $resultSet->hasErrors() === true)
        ) {
            return true;
        }

        return false;
    }
}
