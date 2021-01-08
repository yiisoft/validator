<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Callback;

final class ValidatorFactory implements ValidatorFactoryInterface
{
    private ?TranslatorInterface $translator;
    private ?string $translationDomain;
    private ?string $translationLocale;

    public function __construct(
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        string $translationLocale = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->translationLocale = $translationLocale;
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
     * @param callable|ValidatorRuleInterface $rule
     */
    private function normalizeRule($rule): ValidatorRuleInterface
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof ValidatorRuleInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Rule should be either instance of %s or a callable',
                ValidatorRuleInterface::class
            ));
        }

        if ($rule instanceof TranslatableValidatorRuleInterface) {
            if ($this->translator !== null) {
                $rule = $rule->translator($this->translator);
            }

            if ($this->translationDomain !== null) {
                $rule = $rule->translationDomain($this->translationDomain);
            }

            if ($this->translationLocale !== null) {
                $rule = $rule->translationLocale($this->translationLocale);
            }
        }

        return $rule;
    }
}
