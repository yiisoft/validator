<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Traversable;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Can be used for validation of nested structures.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class StopOnError implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;
    use SkipOnEmptyTrait;

    public function __construct(
        /**
         * Rules for validate value that can be described by:
         * - object that implement {@see RulesProviderInterface};
         * - name of class from whose attributes their will be derived;
         * - array or object implementing the `Traversable` interface that contain {@see RuleInterface} implementations
         *   or closures.
         *
         * `$rules` can be null if validatable value is object. In this case rules will be derived from object via
         * `getRules()` method if object implement {@see RulesProviderInterface} or from attributes otherwise.
         *
         * @var class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|RulesProviderInterface|null
         */
        private iterable|object|string|null $rules = null,

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,

        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if ($this->rules === []) {
            throw new InvalidArgumentException(
                'Rules for StopOnError rule are required.'
            );
        }
    }

    /**
     * @return class-string|iterable|RulesProviderInterface|null
     *
     * @psalm-return RulesProviderInterface|class-string|iterable<mixed, Closure|RuleInterface|array<Closure|RuleInterface>>|null
     */
    public function getRules(): iterable|string|RulesProviderInterface|null
    {
        return $this->rules;
    }

    #[ArrayShape([
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array|null',
    ])]
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => $this->rules === null ? null : (new RulesDumper())->asArray($this->rules),
        ];
    }

    public function getHandlerClassName(): string
    {
        return StopOnErrorHandler::class;
    }
}
