<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Nested;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\RuleValidatorStorage;
use Yiisoft\Validator\ValidationContext;
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
    private ?RuleValidatorStorage $storage;

    public function __construct(RuleValidatorStorage $storage = null)
    {
        // TODO: just for test
        $this->storage = $storage ?? new RuleValidatorStorage();
    }

    public static function getConfigClassName(): string
    {
        return Nested::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!is_object($value) && !is_array($value)) {
            $message = sprintf('Value should be an array or an object. %s given.', gettype($value));
            $result->addError($message);

            return $result;
        }

        $value = (array) $value;

        foreach ($config->rules as $valuePath => $rules) {
            if ($config->errorWhenPropertyPathIsNotFound && !ArrayHelper::pathExists($value, $valuePath)) {
                $result->addError($config->propertyPathIsNotFoundMessage, ['path' => $valuePath]);

                continue;
            }

            $rules = is_array($rules) ? $rules : [$rules];
            $ruleSet = new RuleSet($this->storage, $rules);
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);
            $itemResult = $ruleSet->validate($validatedValue, $config);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $errorValuePath = is_int($valuePath) ? [$valuePath] : explode('.', $valuePath);
                if ($error->getValuePath()) {
                    $errorValuePath = array_merge($errorValuePath, $error->getValuePath());
                }

                $result->addError($error->getMessage(), $errorValuePath);
            }
        }

        return $result;
    }
}
