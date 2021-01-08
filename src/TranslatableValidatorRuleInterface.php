<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

/**
 * Rule represents a single value validation rule.
 */
interface TranslatableValidatorRuleInterface extends ValidatorRuleInterface
{
    public function translator(TranslatorInterface $translator): self;

    public function translationDomain(string $translation): self;

    public function translationLocale(string $locale): self;
}
