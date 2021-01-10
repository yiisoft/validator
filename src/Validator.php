<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    /**
     * @var Rules[] $attributeRules
     */
    private array $attributeRules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $attribute => $ruleSets) {
            if ($ruleSets instanceof Rule) {
                $ruleSets = [$ruleSets];
            } elseif (!is_iterable($ruleSets)) {
                throw new \InvalidArgumentException('Attribute rules should be either an instance of Rule class or an array of instances of Rule class.');
            }
            foreach ($ruleSets as $rule) {
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
                $rules->validate($dataSet->getAttributeValue($attribute), $dataSet)
            );
        }
        return $results;
    }

    /**
     * @param string $attribute
     * @param callable|Rule $rule
     */
    public function addRule(string $attribute, $rule): void
    {
        if (!isset($this->attributeRules[$attribute])) {
            $this->attributeRules[$attribute] = new Rules([]);
        }
        $this->attributeRules[$attribute]->add($rule);
    }

    /**
     * Return all attribute rules as array.
     *
     * For example:
     *
     * ```php
     * [
     *    'amount' => [
     *        [
     *            'number',
     *            'integer' => true,
     *            'max' => 100,
     *            'notANumberMessage' => 'Value must be an integer.',
     *            'tooBigMessage' => 'Value must be no greater than 100.'
     *        ],
     *        ['callback'],
     *    ],
     *    'name' => [
     *        'hasLength',
     *        'max' => 20,
     *        'message' => 'Value must contain at most 20 characters.'
     *    ],
     * ]
     * ```
     *
     * @return array
     */
    public function asArray(?TranslatorInterface $translator = null): array
    {
        $rulesOfArray = [];
        foreach ($this->attributeRules as $attribute => $rules) {
            $rulesOfArray[$attribute] = $rules->asArray($translator);
        }
        return $rulesOfArray;
    }
}
