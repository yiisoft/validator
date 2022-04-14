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
        $result = new Result();
        if (!is_object($value) && !is_array($value)) {
            $message = sprintf('Value should be an array or an object. %s given.', gettype($value));
            $result->addError($message);

            return $result;
        }

        $value = (array)$value;

        foreach ($config->rules as $valuePath => $rules) {
            if ($config->errorWhenPropertyPathIsNotFound && !ArrayHelper::pathExists($value, $valuePath)) {
                $result->addError($config->propertyPathIsNotFoundMessage, ['path' => $valuePath], $valuePath);

                continue;
            }

            $rules = is_array($rules) ? $rules : [$rules];
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);

            $itemResult = $validator->validate($validatedValue, $rules);
//            $itemResult = $validator->validate($validatedValue, [$valuePath => $rules]);

            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
//                $errorValuePath = is_int($valuePath) ? [$valuePath] : explode('.', $valuePath);
//                if ($error->getValuePath()) {
//                    $errorValuePath = array_merge($errorValuePath, $error->getValuePath());
//                }

                $attribute = (string)$valuePath;
                if ($error->getAttribute() !== null) {
                    $attribute .= '.' . $error->getAttribute();
                }
                $result->addError($error->getMessage(), $error->getParameters(), $attribute);
            }
        }

        return $result;
    }
}
