<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\TranslatorInterface;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    private ?TranslatorInterface $translator;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * @param DataSetInterface $dataSet
     * @param Rule[] $rules
     *
     * @return ResultSet
     */
    public function validate(DataSetInterface $dataSet, iterable $rules): ResultSet
    {
        $results = new ResultSet();
        foreach ($rules as $attribute => $attributeRules) {
            $aggregateRule = new Rules($attributeRules);
            if ($this->translator !== null) {
                $aggregateRule = $aggregateRule->withTranslator($this->translator);
            }
            $results->addResult(
                $attribute,
                $aggregateRule->validate($dataSet->getAttributeValue($attribute), $dataSet)
            );
        }
        return $results;
    }

    public function withTranslator(?TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;
        return $new;
    }
}
