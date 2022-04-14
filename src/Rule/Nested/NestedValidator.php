<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Nested;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_array;
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
final class NestedValidator implements RuleValidatorInterface
{
    public static function getRuleClassName(): string
    {
        return Nested::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $compountResult = new Result();
        if (!is_object($value) && !is_array($value)) {
            $message = sprintf('Value should be an array or an object. %s given.', gettype($value));
            $compountResult->addError($message);

            return $compountResult;
        }

        $value = (array)$value;

        $results = [];
        foreach ($config->rules as $valuePath => $rules) {
            $result = new Result((string)$valuePath);

            if ($config->errorWhenPropertyPathIsNotFound && !ArrayHelper::pathExists($value, $valuePath)) {
                $compountResult->addError($config->propertyPathIsNotFoundMessage, ['path' => $valuePath], $valuePath);

                continue;
            }

            $rules = is_array($rules) ? $rules : [$rules];
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);

            $itemResult = $validator->validate($validatedValue, $rules);

            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->merge($error);
            }
            $results[] = $result;
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compountResult->merge($error);
            }
        }

        return $compountResult;
    }
}
