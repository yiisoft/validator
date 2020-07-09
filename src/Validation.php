<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;
use Yiisoft\Validator\Rule\Callback;

final class Validation implements ValidationInterface
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
        return array_map(
            function ($rule) {
                if (is_callable($rule)) {
                    $rule = new Callback($rule);
                }

                if ($this->translator !== null) {
                    $rule = $rule->withTranslator($this->translator);
                }

                if ($this->translationDomain !== null) {
                    $rule = $rule->withTranslationDomain($this->translationDomain);
                }

                if ($this->translationLocale !== null) {
                    $rule = $rule->withTranslationLocale($this->translationLocale);
                }

                return $rule;
            },
            $rules
        );
    }
}
