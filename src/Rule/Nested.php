<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\ValidationContext;
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
final class Nested extends Rule
{
    /**
     * @var Rule[][]
     */
    private iterable $rules;

    private bool $errorWhenPropertyPathIsNotFound = false;
    private string $propertyPathIsNotFoundMessage = 'Property path "{path}" is not found.';

    public static function rule(iterable $rules): self
    {
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        if (empty($rules)) {
            throw new InvalidArgumentException('Rules should not be empty.');
        }

        $rule = new self();
        if ($rule->checkRules($rules)) {
            throw new InvalidArgumentException(sprintf(
                'Each rule should be an instance of %s.',
                RuleInterface::class
            ));
        }

        $rule->rules = $rules;

        return $rule;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
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
     *
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
     *
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
        return $this->fetchOptions($this->rules);
    }

    private function checkRules(array $rules): bool
    {
        return array_reduce(
            $rules,
            fn (bool $carry, $rule) => $carry || (is_array($rule) ? $this->checkRules($rule) : !$rule instanceof RuleInterface),
            false
        );
    }

    private function fetchOptions(array $rules): array
    {
        $result = [];
        foreach ($rules as $attribute => $rule) {
            if (is_array($rule)) {
                $result[$attribute] = $this->fetchOptions($rule);
            } elseif ($rule instanceof ParametrizedRuleInterface) {
                $result[$attribute] = $rule->getOptions();
            } elseif ($rule instanceof RuleInterface) {
                // Just skip the rule that doesn't support parametrizing
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Rules should be an array of rules that implements %s.',
                    ParametrizedRuleInterface::class,
                ));
            }
        }

        return $result;
    }
}
