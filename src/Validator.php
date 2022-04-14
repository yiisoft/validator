<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use JetBrains\PhpStorm\Pure;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use function is_array;
use function is_object;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param Rule[][] $rules
     * @psalm-param iterable<string, Rule[]> $rules
     */
    public function validate($data, iterable $rules = []): Result
    {
        $data = $this->normalizeDataSet($data);
        if ($data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        }

        $context = new ValidationContext($data);
        $result = new Result();

        foreach ($rules as $attribute => $attributeRules) {
            $ruleSet = new RuleSet($attributeRules);
            $tempResult = $ruleSet->validate(
                $data->getAttributeValue($attribute),
                $this,
                $context->withAttribute($attribute)
            );

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), [$attribute, ...$error->getValuePath()]);
            }
        }

        if ($data instanceof PostValidationHookInterface) {
            $data->processValidationResult($result);
        }

        return $result;
    }

    #[Pure]
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
