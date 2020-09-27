<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    /**
     * @var Rules[]
     */
    private array $attributeRules = [];

    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $attribute => $rule) {
            if (!($rule instanceof RuleInterface)) {
                throw new \InvalidArgumentException('Attribute rules should be either an instance of RuleInterface.');
            }
            $this->attributeRules[$attribute] = $rule;
        }
    }

    public function validate(DataSetInterface $dataSet): Errors
    {
        $results = new Errors();
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
     * @param Rule|callable
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
    public function asArray(): array
    {
        $rulesOfArray = [];
        foreach ($this->attributeRules as $attribute => $rule) {
            $options = $rule->getOptions();
            $rulesOfArray[$attribute] = $rule instanceof Rule ?
                array_merge([$rule->getName()], [$options]) : $options[1] ?? [];
        }
        return $rulesOfArray;
    }
}
