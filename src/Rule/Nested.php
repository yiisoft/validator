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
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesDumper;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

use function array_pop;
use function count;
use function implode;
use function is_array;
use function is_int;
use function is_string;
use function ltrim;
use function rtrim;
use function sprintf;

/**
 * Can be used for validation of nested structures.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Nested implements
    SerializableRuleInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    PropagateOptionsInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    private const SEPARATOR = '.';
    private const EACH_SHORTCUT = '*';

    /**
     * @var iterable<iterable<RuleInterface>|RuleInterface>|null
     */
    private iterable|null $rules;

    /**
     * @param class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|object|RulesProviderInterface|null $rules
     *
     * Rules for validate value that can be described by:
     * - object that implement {@see RulesProviderInterface};
     * - name of class from whose attributes their will be derived;
     * - array or object implementing the `Traversable` interface that contain {@see RuleInterface} implementations
     *   or closures.
     *
     * `$rules` can be null if validatable value is object. In this case rules will be derived from object via
     * `getRules()` method if object implement {@see RulesProviderInterface} or from attributes otherwise.
     */
    public function __construct(
        iterable|object|string|null $rules = null,

        /**
         * @var int What visibility levels to use when reading data and rules from validated object.
         */
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        /**
         * @var int What visibility levels to use when reading rules from the class specified in {@see $rules}
         * attribute.
         */
        private int $rulesPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        private string $noRulesWithNoObjectMessage = 'Nested rule without rules can be used for objects only.',
        private string $incorrectDataSetTypeMessage = 'An object data set data can only have an array or an object ' .
        'type.',
        private string $incorrectInputMessage = 'The value must have an array or an object type.',
        private bool $requirePropertyPath = false,
        private string $noPropertyPathMessage = 'Property path "{path}" is not found.',
        private bool $normalizeRules = true,
        private bool $propagateOptions = false,

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
        $this->prepareRules($rules);
    }

    public function getName(): string
    {
        return 'nested';
    }

    /**
     * @return iterable<iterable<RuleInterface>|RuleInterface>|null
     */
    public function getRules(): iterable|null
    {
        return $this->rules;
    }

    public function getPropertyVisibility(): int
    {
        return $this->propertyVisibility;
    }

    public function getNoRulesWithNoObjectMessage(): string
    {
        return $this->noRulesWithNoObjectMessage;
    }

    public function getIncorrectDataSetTypeMessage(): string
    {
        return $this->incorrectDataSetTypeMessage;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getRequirePropertyPath(): bool
    {
        return $this->requirePropertyPath;
    }

    public function getNoPropertyPathMessage(): string
    {
        return $this->noPropertyPathMessage;
    }

    /**
     * @param class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|object|RulesProviderInterface|null $source
     */
    private function prepareRules(iterable|object|string|null $source): void
    {
        if ($source === null) {
            $this->rules = null;

            return;
        }

        if ($source instanceof RulesProviderInterface) {
            $rules = $source->getRules();
        } elseif (!$source instanceof Traversable && !is_array($source)) {
            $rules = (new AttributesRulesProvider($source, $this->rulesPropertyVisibility))->getRules();
        } else {
            $rules = $source;
        }

        self::ensureArrayHasRules($rules);
        $this->rules = $rules;

        if ($this->normalizeRules) {
            $this->normalizeRules();
        }

        if ($this->propagateOptions) {
            $this->propagateOptions();
        }
    }

    /**
     * @psalm-assert iterable<RuleInterface> $rules
     */
    private static function ensureArrayHasRules(iterable &$rules): void
    {
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;

        foreach ($rules as &$rule) {
            if (is_iterable($rule)) {
                self::ensureArrayHasRules($rule);
                continue;
            }
            if (!$rule instanceof RuleInterface) {
                $message = sprintf(
                    'Each rule should be an instance of %s, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                );
                throw new InvalidArgumentException($message);
            }
        }
    }

    private function normalizeRules(): void
    {
        if ($this->rules === null) {
            return;
        }

        /** @var iterable<RuleInterface> $rules */
        $rules = $this->rules;
        if ($rules instanceof Traversable) {
            $rules = iterator_to_array($rules);
        }

        while (true) {
            $breakWhile = true;
            $rulesMap = [];

            foreach ($rules as $valuePath => $rule) {
                if ($valuePath === self::EACH_SHORTCUT) {
                    throw new InvalidArgumentException('Bare shortcut is prohibited. Use "Each" rule instead.');
                }

                $parts = StringHelper::parsePath(
                    (string) $valuePath,
                    delimiter: self::EACH_SHORTCUT,
                    preserveDelimiterEscaping: true
                );
                if (count($parts) === 1) {
                    continue;
                }

                $breakWhile = false;

                $lastValuePath = array_pop($parts);
                $lastValuePath = ltrim($lastValuePath, '.');
                $lastValuePath = str_replace('\\' . self::EACH_SHORTCUT, self::EACH_SHORTCUT, $lastValuePath);

                $remainingValuePath = implode(self::EACH_SHORTCUT, $parts);
                $remainingValuePath = rtrim($remainingValuePath, self::SEPARATOR);

                if (!isset($rulesMap[$remainingValuePath])) {
                    $rulesMap[$remainingValuePath] = [];
                }

                $rulesMap[$remainingValuePath][$lastValuePath] = $rule;
                unset($rules[$valuePath]);
            }

            foreach ($rulesMap as $valuePath => $nestedRules) {
                $rules[$valuePath] = new Each([new self($nestedRules, normalizeRules: false)]);
            }

            if ($breakWhile === true) {
                break;
            }
        }

        $this->rules = $rules;
    }

    public function propagateOptions(): void
    {
        if ($this->rules === null) {
            return;
        }

        $rules = [];
        foreach ($this->rules as $attributeRulesIndex => $attributeRules) {
            if (!is_int($attributeRulesIndex) && !is_string($attributeRulesIndex)) {
                $message = sprintf(
                    'A value path can only have an integer or a string type. %s given',
                    get_debug_type($attributeRulesIndex),
                );

                throw new InvalidArgumentException($message);
            }

            /** @var iterable<RuleInterface>|null $attributeRules */
            foreach ($attributeRules ?? [] as $attributeRule) {
                if ($attributeRule instanceof SkipOnEmptyInterface) {
                    $attributeRule = $attributeRule->skipOnEmpty($this->skipOnEmpty);
                }
                if ($attributeRule instanceof SkipOnErrorInterface) {
                    $attributeRule = $attributeRule->skipOnError($this->skipOnError);
                }
                if ($attributeRule instanceof WhenInterface) {
                    $attributeRule = $attributeRule->when($this->when);
                }

                $rules[$attributeRulesIndex][] = $attributeRule;

                if ($attributeRule instanceof PropagateOptionsInterface) {
                    $attributeRule->propagateOptions();
                }
            }
        }

        $this->rules = $rules;
    }

    #[ArrayShape([
        'requirePropertyPath' => 'bool',
        'noPropertyPathMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array|null',
    ])]
    public function getOptions(): array
    {
        return [
            'noRulesWithNoObjectMessage' => $this->noRulesWithNoObjectMessage,
            'incorrectDataSetTypeMessage' => $this->incorrectDataSetTypeMessage,
            'incorrectInputMessage' => $this->incorrectInputMessage,
            'requirePropertyPath' => $this->getRequirePropertyPath(),
            'noPropertyPathMessage' => [
                'message' => $this->getNoPropertyPathMessage(),
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => $this->rules === null ? null : (new RulesDumper())->asArray($this->rules),
        ];
    }

    public function getHandlerClassName(): string
    {
        return NestedHandler::class;
    }
}
