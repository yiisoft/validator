<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Traversable;
use Yiisoft\Validator\PreValidatableRuleInterface;
use Yiisoft\Validator\Rule\Trait\PreValidatableTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;
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
final class Nested implements ParametrizedRuleInterface, PreValidatableRuleInterface
{
    use HandlerClassNameTrait;
    use PreValidatableTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var RuleInterface[][]
         */
        private iterable $rules = [],
        private bool $errorWhenPropertyPathIsNotFound = false,
        private string $propertyPathIsNotFoundMessage = 'Property path "{path}" is not found.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
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

    /**
     * @return iterable
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function isErrorWhenPropertyPathIsNotFound(): bool
    {
        return $this->errorWhenPropertyPathIsNotFound;
    }

    /**
     * @return string
     */
    public function getPropertyPathIsNotFoundMessage(): string
    {
        return $this->propertyPathIsNotFoundMessage;
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
        return (new RulesDumper())->asArray($this->rules);
    }
}
