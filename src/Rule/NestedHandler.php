<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_int;
use function is_object;

/**
 * A handler for {@see Nested} rule. Validates nested structures.
 */
final class NestedHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Nested) {
            throw new UnexpectedRuleException(Nested::class, $rule);
        }

        /** @var mixed $value */
        $value = $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY) ?? $value;

        if ($rule->getRules() === null) {
            if (!is_object($value)) {
                return (new Result())->addError($rule->getNoRulesWithNoObjectMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'type' => get_debug_type($value),
                ]);
            }

            $dataSet = new ObjectDataSet($value, $rule->getValidatedObjectPropertyVisibility());

            return $context->validate($dataSet);
        }

        if (is_array($value)) {
            $data = $value;
        } elseif (is_object($value)) {
            $data = (new ObjectDataSet($value, $rule->getValidatedObjectPropertyVisibility()))->getData();
            if ($data === null) {
                return (new Result())->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'type' => get_debug_type($data),
                ]);
            }
        } else {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $compoundResult = new Result();

        foreach ($rule->getRules() as $valuePath => $rules) {
            if ($rule->isPropertyPathRequired() && !ArrayHelper::pathExists($data, $valuePath)) {
                if (is_int($valuePath)) {
                    $valuePathList = [$valuePath];
                } else {
                    /** @var list<string> $valuePathList */
                    $valuePathList = StringHelper::parsePath($valuePath);
                }

                $compoundResult->addError(
                    $rule->getNoPropertyPathMessage(),
                    [
                        'path' => $valuePath,
                        'attribute' => $context->getTranslatedAttribute(),
                    ],
                    $valuePathList,
                );

                continue;
            }

            /** @var mixed $validatedValue */
            $validatedValue = ArrayHelper::getValueByPath($data, $valuePath);

            $itemResult = $context->validate($validatedValue, $rules);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                if (is_int($valuePath)) {
                    $valuePathList = [$valuePath];
                } else {
                    /** @var list<string> $valuePathList */
                    $valuePathList = StringHelper::parsePath($valuePath);
                }

                if (!empty($valuePathList)) {
                    array_push($valuePathList, ...$error->getValuePath());
                }

                $compoundResult->addError($error->getMessage(), $error->getParameters(), $valuePathList);
            }
        }

        return $compoundResult;
    }
}
