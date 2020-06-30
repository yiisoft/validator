<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;
use Yiisoft\I18n\TranslatorInterface;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
class Validator implements ValidatorInterface
{
    private ?TranslatorInterface $translator;
    private ?string $translationDomain;
    private ?string $translationLocale;

    /**
     * @var Rules[]
     */
    private array $attributeRules = [];

    public function __construct(
        iterable $rules = [],
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        string $translationLocale = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->translationLocale = $translationLocale;
    }

    public function validate(DataSetInterface $dataSet): ResultSet
    {
        $results = new ResultSet();
        foreach ($this->attributeRules as $attribute => $rules) {
            $results->addResult(
                $attribute,
                $rules->validate($dataSet->getAttributeValue($attribute), $dataSet)
            );
        }
        return $results;
    }

    public function addRule(string $attribute, Rule $rule): void
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules(
                [],
                $this->translator,
                $this->translationDomain,
                $this->translationLocale
            );
        }

        $this->attributeRules[$attribute]->add($rule);
    }

    public function addRules(iterable $rules = []): void
    {
        foreach ($rules as $attribute => $ruleSets) {
            foreach ($ruleSets as $rule) {
                if (is_callable($rule)) {
                    $rule = new Callback($rule);
                }
                $this->addRule($attribute, $rule);
            }
        }
    }
}
