<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Traversable;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;

use Yiisoft\Validator\ValidationContext;

use function is_array;

/**
 * Can be used for validation of nested structures.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Nested implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>
         */
        private iterable $rules = [],
        private bool $errorWhenPropertyPathIsNotFound = false,
        private string $propertyPathIsNotFoundMessage = 'Property path "{path}" is not found.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
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
     * @return iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>
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

    public function getHandlerClassName(): string
    {
        return NestedHandler::class;
    }
}
