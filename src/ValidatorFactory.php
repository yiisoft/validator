<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

final class ValidatorFactory implements ValidatorFactoryInterface
{
    private ?TranslatorInterface $translator;
    private ?string $translationDomain;
    private ?string $translationLocale;

    public function __construct(
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        string $translationLocale = null
    )
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->translationLocale = $translationLocale;
    }

    public function create(): ValidatorInterface
    {
        $validator = new Validator();

        if ($this->translator !== null) {
            $validator = $validator->withTranslator($this->translator);
        }

        if ($this->translationDomain !== null) {
            $validator = $validator->translationDomain($this->translationDomain);
        }

        if ($this->translationLocale !== null) {
            $validator = $validator->translationLocale($this->translationLocale);
        }

        return $validator;
    }
}
