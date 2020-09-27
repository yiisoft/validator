<?php

namespace Yiisoft\Validator;

use Yiisoft\I18n\TranslatorInterface;

interface TranslatableErrorInterface
{
    public function translator(TranslatorInterface $translator);
    public function translationDomain(string $translation);
    public function translationLocale(string $locale);
}
