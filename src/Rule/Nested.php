<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rules;
use function is_array;
use function is_object;

/**
 * Nested rule can be used for validation of nested structures.
 *
 * For example we have an inbound request with the following structure:
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
 * So to make validation with Nested rule we can configure it like this:
 *
 * ```php
 * $rule = new Nested([
 *     'author.age' => [
 *         (new Number())->min(20),
 *     ],
 *     'author.name' => [
 *         (new HasLength())->min(3),
 *     ],
 * ]);
 * ```
 */
class Nested extends Rule
{
    /**
     * @var Rule[][]
     */
    private iterable $rules;

    private bool $errorWhenPropertyPathIsNotFound = false;
    private string $propertyPathIsNotFoundMessage = 'Property path "{path}" is not found.';

    public function __construct(iterable $rules)
    {
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        if (empty($rules)) {
            throw new InvalidArgumentException('Rules should not be empty.');
        }
        if ($this->checkRules($rules)) {
            throw new InvalidArgumentException(sprintf(
                'Each rule should be an instance of %s.',
                Rule::class
            ));
        }
        $this->rules = $rules;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        if (!is_object($value) && !is_array($value)) {
            $result->addError(sprintf(
                'Value should be an array or an object. %s given.',
                gettype($value)
            ));
            return $result;
        }
        $value = (array) $value;

        foreach ($this->rules as $valuePath => $rules) {
            $rulesSet = is_array($rules) ? $rules : [$rules];
            if ($this->errorWhenPropertyPathIsNotFound && !ArrayHelper::pathExists($value, $valuePath)) {
                $result->addError(
                    $this->formatMessage(
                        $this->propertyPathIsNotFoundMessage,
                        [
                            'path' => $valuePath,
                        ]
                    )
                );
                continue;
            }
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);
            $aggregateRule = new Rules($rulesSet);
            $itemResult = $aggregateRule->validate($validatedValue);
            if ($itemResult->isValid() === false) {
                foreach ($itemResult->getErrors() as $error) {
                    $result->addError($error);
                }
            }
        }

        return $result;
    }

    /**
     * @param bool $value If absence of nested property should be considered an error. Default is `false`.
     * @return self
     */
    public function errorWhenPropertyPathIsNotFound(bool $value): self
    {
        $new = clone $this;
        $new->errorWhenPropertyPathIsNotFound = $value;
        return $new;
    }

    /**
     * @param string $message A message to use when nested property is absent.
     * @return $this
     */
    public function propertyPathIsNotFoundMessage(string $message): self
    {
        $new = clone $this;
        $new->propertyPathIsNotFoundMessage = $message;
        return $new;
    }

    public function getOptions(): array
    {
        return $this->rules->asArray();
    }

    private function checkRules(array $rules): bool
    {
        return array_reduce(
            $rules,
            fn (bool $carry, $rule) => $carry || is_array($rule) ? $this->checkRules($rule) : !$rule instanceof Rule,
            false
        );
    }
}
