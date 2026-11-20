<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function array_key_exists;
use function array_slice;
use function is_array;
use function is_int;
use function is_object;

/**
 * A handler for {@see Nested} rule. Validates nested structures.
 */
final class NestedHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Nested) {
            throw new UnexpectedRuleException(Nested::class, $rule);
        }

        if ($rule->getRules() === null) {
            if (!is_object($value)) {
                return (new Result())->addError($rule->getNoRulesWithNoObjectMessage(), [
                    'property' => $context->getTranslatedProperty(),
                    'Property' => $context->getCapitalizedTranslatedProperty(),
                    'type' => get_debug_type($value),
                ]);
            }

            $dataSet = new ObjectDataSet($value, $rule->getValidatedObjectPropertyVisibility());

            return $context->validate($dataSet);
        }

        $value = $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY) ?? $value;

        if (is_array($value)) {
            $data = $value;
        } elseif (is_object($value)) {
            $data = (new ObjectDataSet($value, $rule->getValidatedObjectPropertyVisibility()))->getData();
            if ($data === null) {
                return (new Result())->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'property' => $context->getTranslatedProperty(),
                    'Property' => $context->getCapitalizedTranslatedProperty(),
                    'type' => get_debug_type($data),
                ]);
            }
        } else {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        $compoundResult = new Result();

        foreach ($rule->getRules() as $valuePath => $rules) {
            $valuePathList = is_int($valuePath)
                ? [$valuePath]
                : StringHelper::parsePath($valuePath);

            if ($rule->isPropertyPathRequired() && !ArrayHelper::pathExists($data, $valuePath)) {
                $compoundResult->addError(
                    $rule->getNoPropertyPathMessage(),
                    [
                        'path' => $valuePath,
                        'property' => $context->getTranslatedProperty(),
                        'Property' => $context->getCapitalizedTranslatedProperty(),
                    ],
                    $valuePathList,
                );

                continue;
            }

            $property = end($valuePathList);
            $validateValueItself = is_int($valuePath) || (string) (int) $property === $property;

            $itemResult = $validateValueItself
                ? $context->validate(
                    ArrayHelper::getValueByPath($data, $valuePath),
                    $rules,
                )
                : $context->validate(
                    $this->getPropertyScopeData($data, $valuePathList),
                    [$property => $rules],
                );

            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $errorValuePath = $validateValueItself
                    ? [...$valuePathList, ...$error->getValuePath()]
                    : [...$valuePathList, ...array_slice($error->getValuePath(), 1)];

                $compoundResult->addErrorWithoutPostProcessing(
                    $error->getMessage(),
                    $error->getParameters(),
                    $errorValuePath,
                );
            }
        }

        if ($value instanceof PostValidationHookInterface) {
            $value->processValidationResult($compoundResult);
        }

        return $compoundResult;
    }

    /**
     * Get the data of the scope a property with a given value path belongs to, so it also contains sibling
     * properties. Only arrays are traversed, any other intermediate value is treated as a missing path.
     *
     * @param array $data The data to get the scope from.
     * @param array<array-key, int|string> $valuePathList A parsed value path of the validated property.
     *
     * @return array The scope data. An empty array when the scope cannot be resolved.
     */
    private function getPropertyScopeData(array $data, array $valuePathList): array
    {
        $scopeData = $data;
        foreach (array_slice($valuePathList, 0, -1) as $key) {
            if (!is_array($scopeData) || !array_key_exists($key, $scopeData)) {
                return [];
            }
            $scopeData = $scopeData[$key];
        }

        return is_array($scopeData) ? $scopeData : [];
    }
}
