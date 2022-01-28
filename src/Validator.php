<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use function is_array;
use function is_object;

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
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param Rule[][] $rules
     * @psalm-param iterable<string, Rule[]> $rules
     *
     * @return ResultSet
     */
    public function validate($data, iterable $rules = []): ResultSet
    {
        $data = $this->normalizeDataSet($data);
        if ($data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        }

        $context = new ValidationContext($data);
        $resultSet = new ResultSet();

        foreach ($rules as $attribute => $attributeRules) {
            $aggregatedRule = new Rules($attributeRules);
            if ($this->formatter !== null) {
                $aggregatedRule = $aggregatedRule->withFormatter($this->formatter);
            }

            $resultSet->addResult(
                $attribute,
                $aggregatedRule->validate($data->getAttributeValue($attribute), $context->withAttribute($attribute))
            );
        }

        if ($data instanceof PostValidationHookInterface) {
            $data->processValidationResult($resultSet);
        }

        return $resultSet;
    }

    public function withFormatter(?FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        return $new;
    }

    private function normalizeDataSet($data): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $data;
        }

        if (is_object($data) || is_array($data)) {
            return new ArrayDataSet((array) $data);
        }

        return new ScalarDataSet($data);
    }
}
