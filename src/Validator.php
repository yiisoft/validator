<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;
use Yiisoft\I18n\TranslatorInterface;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
class Validator
{
    private ?TranslatorInterface $translator = null;
    private string $domain;

    /**
     * @var Rules[]
     */
    private array $attributeRules = [];

    public function __construct(
        iterable $rules = [],
        TranslatorInterface $translator = null,
        string $domain = null
    ) {
        $this->translator = $translator;
        $this->domain = $domain ?? 'validators';

        foreach ($rules as $attribute => $ruleSets) {
            foreach ($ruleSets as $rule) {
                if (is_callable($rule)) {
                    $rule = new Callback($rule);
                }
                $this->addRule($attribute, $rule);
            }
        }
    }

    public function validate(DataSetInterface $dataSet): ResultSet
    {
        $results = new ResultSet();
        foreach ($this->attributeRules as $attribute => $rules) {
            $results->addResult(
                $attribute,
                $this->translateMessages($rules->validate($dataSet->getAttributeValue($attribute)))
            );
        }
        return $results;
    }

    public function addRule(string $attribute, Rule $rule): void
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules();
        }

        $this->attributeRules[$attribute]->add($rule);
    }

    private function translateMessages(RuleResult $ruleResult): Result
    {
        $result = new Result();

        if ($ruleResult->isValid() === false) {
            foreach ($ruleResult->getErrors() as $error) {
                [$message, $arguments] = $error;
                $result->addError($this->translator->translate($message, $arguments, $this->domain));
            }
        }

        return $result;
    }
}
