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
        $resultSet = new ResultSet();
        foreach ($rules as $attribute => $attributeRules) {
            $aggregateRule = new Rules($attributeRules);
            if ($this->formatter !== null) {
                $aggregateRule = $aggregateRule->withFormatter($this->formatter);
            }
            $resultSet->addResult(
                $attribute,
                $aggregateRule->validate($dataSet->getAttributeValue($attribute), $context->withAttribute($attribute))
            );
        }
        if ($dataSet instanceof PostValidationHookInterface) {
            $dataSet->processValidationResult($resultSet);
        }
        return $resultSet;
    }

    public function withFormatter(?FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        return $new;
    }
}
