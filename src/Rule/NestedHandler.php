<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
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

        if ($rule->getRules() === null) {
            if (!is_object($value)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Nested rule without rules could be used for objects only. %s given.',
                        get_debug_type($value)
                    )
                );
            }

            $dataSet = new ObjectDataSet($value, $rule->getPropertyVisibility());

            return $context->getValidator()->validate($dataSet, $dataSet->getRules(), $context);
        }

        if (is_array($value)) {
            $data = $value;
        } elseif (is_object($value)) {
            $data = (new ObjectDataSet($value, $rule->getPropertyVisibility()))->getData();
        } else {
            $message = sprintf(
                'Value should be an array or an object. %s given.',
                get_debug_type($value)
            );
            $result = new Result();
            $result->addError(
                message:$message,
                parameters: ['value' => $value]
            );
            return  $result;
        }

        $compoundResult = new Result();
        foreach ($rule->getRules() as $valuePath => $rules) {
            if ($rule->getRequirePropertyPath() && !ArrayHelper::pathExists($data, $valuePath)) {
                /**
                 * @psalm-suppress InvalidScalarArgument
                 */
                $compoundResult->addError(
                    message: $rule->getNoPropertyPathMessage(),
                    valuePath:  StringHelper::parsePath($valuePath),
                    parameters: ['path' => $valuePath, 'value' => $data]
                );
                continue;
            }

            $validatedValue = ArrayHelper::getValueByPath($data, $valuePath);
            $rules = is_iterable($rules) ? $rules : [$rules];

            $itemResult = $context->getValidator()->validate($validatedValue, $rules, $context);

            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $errorValuePath = is_int($valuePath) ? [$valuePath] : StringHelper::parsePath($valuePath);
                if (!empty($error->getValuePath())) {
                    array_push($errorValuePath, ...$error->getValuePath());
                }
                /**
                 * @psalm-suppress InvalidScalarArgument
                 */
                $compoundResult->addError($error->getMessage(), $errorValuePath, $error->getParameters());
            }
        }

        return $compoundResult;
    }
}
