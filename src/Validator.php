<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    private ?FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param DataSetInterface|RulesProviderInterface $dataSet
     * @param Rule[][] $rules
     * @psalm-param iterable<string, Rule[]> $rules
     *
     * @return ResultSet
     */
    public function validate(DataSetInterface $dataSet, iterable $rules = []): ResultSet
    {
        if ($dataSet instanceof RulesProviderInterface) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $rules = $dataSet->getRules();
        }
        $context = new ValidationContext($dataSet);
        $results = new ResultSet();
        foreach ($rules as $attribute => $attributeRules) {
            $aggregateRule = new Rules($attributeRules);
            if ($this->formatter !== null) {
                $aggregateRule = $aggregateRule->withFormatter($this->formatter);
            }
            $results->addResult(
                $attribute,
                $aggregateRule->validate($dataSet->getAttributeValue($attribute), $context->withAttribute($attribute))
            );
        }
        return $results;
    }

    public function withFormatter(?FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        return $new;
    }
}
