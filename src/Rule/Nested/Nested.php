<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Nested;

use Attribute;
use Closure;
use InvalidArgumentException;
use Traversable;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\RuleInterface;
use function is_array;

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
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Nested implements RuleInterface
{
    use RuleNameTrait;

    public function __construct(
        /**
         * @var Rule[][]
         */
        public iterable $rules = [],
        public bool $errorWhenPropertyPathIsNotFound = false,
        public string $propertyPathIsNotFoundMessage = 'Property path "{path}" is not found.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        if (empty($rules)) {
            throw new InvalidArgumentException('Rules must not be empty.');
        }

        if ($this->checkRules($rules)) {
            $message = sprintf('Each rule should be an instance of %s.', RuleInterface::class);
            throw new InvalidArgumentException($message);
        }

        $this->rules = $rules;
    }

    private function checkRules(array $rules): bool
    {
        return array_reduce(
            $rules,
            function (bool $carry, $rule) {
                return $carry || (is_array($rule) ? $this->checkRules($rule) : !$rule instanceof RuleInterface);
            },
            false
        );
    }

    public function getOptions(): array
    {
        return $this->fetchOptions($this->rules);
    }

    private function fetchOptions(iterable $rules): array
    {
        $result = [];
        foreach ($rules as $attribute => $rule) {
            if (is_array($rule)) {
                $result[$attribute] = $this->fetchOptions($rule);
            } elseif ($rule instanceof RuleInterface) {
                $result[$attribute] = $rule->getOptions();
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Rules should be an array of rules that implements %s.',
                    RuleInterface::class,
                ));
            }
        }

        return $result;
    }
}
