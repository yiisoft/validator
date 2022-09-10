<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionProperty;
use Traversable;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

use function array_pop;
use function count;
use function implode;
use function is_array;
use function ltrim;
use function rtrim;
use function sprintf;

/**
 * Can be used for validation of nested structures.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Lazy implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface
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
                'Rules for Lazy rule are required.'
            );
        }
    }

    /**
     * @return iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>
     */
    public function getRules(): iterable
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
        return LazyHandler::class;
    }
}
