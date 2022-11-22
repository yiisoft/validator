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
 * Can be used for validation of nested structures.
 *
 * For example, we have an inbound request with the following structure:
 *
 * ```php
 * $request = [
 *     'author' => [
 *         'name' => 'Dmitry',
 *         'age' => 18,
 *     ],
 * ];
 * ```
 *
 * So to make validation we can configure it like this:
 *
 * ```php
 * $rule = new Nested([
 *     'author' => new Nested([
 *         'name' => [new HasLength(min: 3)],
 *         'age' => [new Number(min: 18)],
 *     )];
 * ]);
 * ```
 */
final class NestedHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Nested) {
            throw new UnexpectedRuleException(Nested::class, $rule);
        }

        $compoundResult = new Result();

        if ($rule->getRules() === null) {
            if (!is_object($value)) {
                return $compoundResult->addError($rule->getNoRulesWithNoObjectMessage(), [
                    'attribute' => $context->getAttribute(),
                    'type' => get_debug_type($value),
                ]);
            }

            $dataSet = new ObjectDataSet($value, $rule->getPropertyVisibility());

            return $context->getValidator()->validate($dataSet);
        }

        if (is_array($value)) {
            $data = $value;
        } elseif (is_object($value)) {
            /** @var mixed $data */
            $data = (new ObjectDataSet($value, $rule->getPropertyVisibility()))->getData();
            if (!is_array($data) && !is_object($data)) {
                return $compoundResult->addError($rule->getIncorrectDataSetTypeMessage(), [
                    'type' => get_debug_type($data),
                ]);
            }
        } else {
            return $compoundResult->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $results = [];
        /** @var int|string $valuePath */
        foreach ($rule->getRules() as $valuePath => $rules) {
            if (is_array($data) && $rule->getRequirePropertyPath() && !ArrayHelper::pathExists($data, $valuePath)) {
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
                        'attribute' => $context->getAttribute(),
                    ],
                    $valuePathList,
                );

                continue;
            }

            /** @var mixed $validatedValue */
            $validatedValue = ArrayHelper::getValueByPath($data, $valuePath);
            $rules = is_iterable($rules) ? $rules : [$rules];

            $itemResult = $context->getValidator()->validate($validatedValue, $rules);
            if ($itemResult->isValid()) {
                continue;
            }

            $result = new Result();
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

                $result->addError($error->getMessage(), $error->getParameters(), $valuePathList);
            }
            $results[] = $result;
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getParameters(), $error->getValuePath());
            }
        }

        return $compoundResult;
    }
}
