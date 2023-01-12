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
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Helper\PropagateOptionsHelper;
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\Tests\Rule\NestedTest;
use Yiisoft\Validator\WhenInterface;

use function array_pop;
use function count;
use function implode;
use function is_string;
use function ltrim;
use function rtrim;
use function sprintf;

/**
 * Used to define rules for validation of nested structures:
 *
 * - For one-to-one relation, using `Nested` rule is enough.
 * - One-to-many and many-to-many relations require pairing with {@see Each} rule.
 *
 * An example with blog post:
 *
 * ```php
 * $rule = new Nested([
 *     'title' => [new HasLength(max: 255)],
 *     // One-to-one relation
 *     'author' => new Nested([
 *         'name' => [new HasLength(min: 1)],
 *     ]),
 *     // One-to-many relation
 *     'files' => new Each([
 *          new Nested([
 *             'url' => [new Url()],
 *         ]),
 *     ]),
 * ]);
 * ```
 *
 * There is an alternative way to write this using dot notation and shortcuts:
 *
 * ```php
 * $rule = new Nested([
 *     'title' => [new HasLength(max: 255)],
 *     'author.name' => [new HasLength(min: 1)],
 *     'files.*.url' => [new Url()],
 * ]);
 * ```
 *
 * Note that the maximum nesting level is 2, a deeper one requires wrapping with another `Nested` instance or using
 * short syntax shown above. This will work:
 *
 * ```php
 * $rule = new Nested([
 *     'author' => [
 *         'name' => [new HasLength(min: 1)],
 *     ],
 * ]);
 * ```
 *
 * But this will not:
 *
 * ```php
 * $rule = new Nested([
 *     'author' => [
 *         'name' => [
 *             'surname' => [new HasLength(min: 1)],
 *         ],
 *     ],
 * ]);
 * ```
 *
 * Also it's possible to omit arrays for single rules:
 *
 *  * ```php
 * $rules = [
 *     new Nested([
 *         'author' => new Nested([
 *             'name' => new HasLength(min: 1),
 *         ]),
 *     ]),
 * ];
 * ```
 *
 * For more examples please refer to the guide.
 *
 * It's also possible to use DTO objects with PHP attributes, see {@see ObjectDataSet} documentation and guide for
 * details.
 *
 * Supports propagation of options (see {@see PropagateOptionsHelper::propagate()} for available options and
 * requirements).
 *
 * @see NestedHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 * @psalm-type ReadyRulesType = array<array<RuleInterface>|RuleInterface>|null
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Nested implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    PropagateOptionsInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * A character acting as a separator when using alternative (short) syntax.
     */
    private const SEPARATOR = '.';
    /**
     * A character acting as a shortcut when using alternative (short) syntax with {@see Nested} and {@see Each}
     * combinations.
     */
    private const EACH_SHORTCUT = '*';

    /**
     * @var array|null A set of ready to use rule instances. The 1st level is always an array of rules, the 2nd level is
     * either an array of rules or a single rule.
     * @psalm-var ReadyRulesType
     */
    private array|null $rules;

    /**
     * @param iterable|object|string|null $rules Rules for validating nested structure. The following types are
     * supported:
     *
     * - Array or object implementing {@see Traversable} interface containing rules. Either iterables containing
     * {@see RuleInterface} implementations or a single rule instances are expected. The maximum nesting level is 2.
     * Another instance of `Nested`can be used for further nesting. All iterables regardless of the nesting level will
     * be converted to arrays at the end.
     * - Object implementing {@see RulesProviderInterface}.
     * - Name of a class containing rules declared via PHP attributes.
     * - `null` if validated value is an object. It can either implement {@see RulesProviderInterface} or contain rules
     * declared via PHP attributes.
     * @psalm-param iterable|object|class-string|null $rules
     *
     * @param int $validatedObjectPropertyVisibility Visibility levels to use for parsed properties when validated value
     * is an object providing rules / data. For example: public and protected only, this means that the rest (private
     * ones) will be skipped. Defaults to all visibility levels (public, protected and private). See
     * {@see ObjectDataSet} for details on providing rules / data in validated object and {@see ObjectParser} for
     * overview how parsing works.
     * @psalm-param int-mask-of<ReflectionProperty::IS_*> $validatedObjectPropertyVisibility
     *
     * @param int $rulesSourceClassPropertyVisibility Visibility levels to use for parsed properties when {@see $rules}
     * source is a name of the class providing rules. For example: public and protected only, this means that the rest
     * (private ones) will be skipped. Defaults to all visibility levels (public, protected and private). See
     * {@see ObjectDataSet} for details on providing rules via class and {@see ObjectParser} for overview how parsing
     * works.
     * @psalm-param int-mask-of<ReflectionProperty::IS_*> $rulesSourceClassPropertyVisibility
     *
     * @param string $noRulesWithNoObjectMessage Error message used when validation fails because the validated value is
     * not an object and the rules were not explicitly specified via {@see $rules}:
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectDataSetTypeMessage Error message used when validation fails because the validated value
     * is an object providing wrong type of data (neither array nor an object).
     *
     * You may use the following placeholders in the message:
     *
     * - `{type}`: the type of the data set retrieved from the validated object.
     * @param string $incorrectInputMessage Error message used when validation fails because the validated value is
     * neither an array nor an object.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param bool $requirePropertyPath Whether to require a single data item to be passed in data according to declared
     * nesting level structure (all keys in the sequence must be the present). Used only when validated value is an
     * array. Enabled by default. See {@see $noPropertyPathMessage} for customization of error message.
     * @param string $noPropertyPathMessage Error message used when validation fails because {@see $requirePropertyPath}
     * option was enabled and the validated array contains missing data item.
     *
     * You may use the following placeholders in the message:
     *
     * - `{path}`: the path of the value being validated. Can be either a simple key of integer / string type for a
     * single nesting level or a sequence of keys concatenated using dot notation (see {@see SEPARATOR}).
     * - `{attribute}`: the translated label of the attribute being validated.
     * @param bool $handleEachShortcut Whether to handle {@see EACH_SHORTCUT}. Enabled by default meaning shortcuts are
     * supported. Can be disabled if they are not used to prevent additional checks and improve performance.
     * @param bool $propagateOptions Whether the propagation of options is enabled (see
     * {@see PropagateOptionsHelper::propagate()} for supported options and requirements). Disabled by default.
     * @param bool|callable|null $skipOnEmpty Whether to skip this `Nested` rule with all defined {@see $rules} if the
     * validated value is empty / not passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this `Nested` rule with all defined {@see $rules} if any of the previous
     * rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when  A callable to define a condition for applying this `Nested` rule with all defined
     * {@see $rules}. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        iterable|object|string|null $rules = null,
        private int $validatedObjectPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        private int $rulesSourceClassPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        private string $noRulesWithNoObjectMessage = 'Nested rule without rules can be used for objects only.',
        private string $incorrectDataSetTypeMessage = 'An object data set data can only have an array or an object ' .
        'type.',
        private string $incorrectInputMessage = 'The value must have an array or an object type.',
        private bool $requirePropertyPath = false,
        private string $noPropertyPathMessage = 'Property "{path}" is not found.',
        private bool $handleEachShortcut = true,
        private bool $propagateOptions = false,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
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

    /**
     * Gets visibility levels to use for parsed properties when validated value is an object providing rules / data.
     * Defaults to all visibility levels (public, protected and private)
     *
     * @return int A number representing visibility levels.
     * @psalm-return int-mask-of<ReflectionProperty::IS_*>
     *
     * @see $validatedObjectPropertyVisibility
     */
    public function getValidatedObjectPropertyVisibility(): int
    {
        return $this->validatedObjectPropertyVisibility;
    }

    /**
     * Gets error message used when validation fails because the validated value is not an object and the rules were not
     * explicitly specified via {@see $rules}.
     *
     * @return string Error message / template.
     *
     * @see $incorrectInputMessage
     */
    public function getNoRulesWithNoObjectMessage(): string
    {
        return $this->noRulesWithNoObjectMessage;
    }

    /**
     * Gets error message used when validation fails because the validated value is an object providing wrong type of
     * data (neither array nor an object).
     *
     * @return string Error message / template.
     *
     * @see $incorrectDataSetTypeMessage
     */
    public function getIncorrectDataSetTypeMessage(): string
    {
        return $this->incorrectDataSetTypeMessage;
    }

    /**
     * Gets error message used when validation fails because the validated value is neither an array nor an object.
     *
     * @return string Error message / template.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Whether to require a single data item to be passed in data according to declared nesting level structure (all
     * keys in the sequence must be the present). Enabled by default.
     *
     * @return bool `true` if required and `false` otherwise.
     *
     * @see $requirePropertyPath
     */
    public function isPropertyPathRequired(): bool
    {
        return $this->requirePropertyPath;
    }

    /**
     * Gets error message used when validation fails because {@see $requirePropertyPath} option was enabled and the
     * validated array contains missing data item.
     *
     * @return string Error message / template.
     *
     * @see $getNoPropertyPathMessage
     */
    public function getNoPropertyPathMessage(): string
    {
        return $this->noPropertyPathMessage;
    }

    /**
     * Prepares raw rules passed in the constructor for usage in handler. As a result, {@see $rules} property will
     * contain ready to use rules.
     *
     * @param iterable|object|string|null $source Raw rules passed in the constructor.
     *
     * @throws InvalidArgumentException When rules' source has wrong type.
     * @throws InvalidArgumentException When source contains items that are not rules.
     */
    private function prepareRules(iterable|object|string|null $source): void
    {
        if ($source === null) {
            $this->rules = null;

            return;
        }

        if ($source instanceof RulesProviderInterface) {
            $rules = $source->getRules();
        } elseif (is_string($source) && class_exists($source)) {
            $rules = (new AttributesRulesProvider($source, $this->rulesSourceClassPropertyVisibility))->getRules();
        } elseif (is_iterable($source)) {
            $rules = $source;
        } else {
            throw new InvalidArgumentException(
                'The $rules argument passed to Nested rule can be either: a null, an object implementing ' .
                'RulesProviderInterface, a class string or an iterable.'
            );
        }

        self::ensureArrayHasRules($rules);
        /** @psalm-var ReadyRulesType $rules */
        $this->rules = $rules;

        if ($this->handleEachShortcut) {
            $this->handleEachShortcut();
        }

        if ($this->propagateOptions) {
            $this->propagateOptions();
        }
    }

    /**
     * Recursively checks that every item of source iterable is a valid rule instance ({@see RuleInterface}). As a
     * result, all iterables will be converted to arrays at the end, while single rules will be kept as is.
     *
     * @param iterable $rules Source iterable that will be checked and converted to array (so it's passed by reference).
     *
     * @throws InvalidArgumentException When iterable contains items that are not rules.
     */
    private static function ensureArrayHasRules(iterable &$rules): void
    {
        if ($rules instanceof Traversable) {
            $rules = iterator_to_array($rules);
        }

        /** @var mixed $rule */
        foreach ($rules as &$rule) {
            if (is_iterable($rule)) {
                self::ensureArrayHasRules($rule);
            } elseif (!$rule instanceof RuleInterface) {
                $message = sprintf(
                    'Every rule must be an instance of %s, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                );

                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * Converts rules defined with {@see EACH_SHORTCUT} to separate `Nested` and `Each` rules.
     */
    private function handleEachShortcut(): void
    {
        /** @var RuleInterface[] $rules Conversion to array is done in {@see ensureArrayHasRules()}. */
        $rules = $this->rules;
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

                /**
                 * Might be a bug of XDebug, because these lines are covered by tests.
                 *
                 * @see NestedTest::dataWithOtherNestedAndEach() for test cases prefixed with "withShortcut".
                 */
                // @codeCoverageIgnoreStart
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
                // @codeCoverageIgnoreEnd
            }

            foreach ($rulesMap as $valuePath => $nestedRules) {
                /**
                 * Might be a bug of XDebug, because this line is covered by tests.
                 *
                 * @see NestedTest::dataWithOtherNestedAndEach() for test cases prefixed with "withShortcut".
                 */
                // @codeCoverageIgnoreStart
                $rules[$valuePath] = new Each([new self($nestedRules, handleEachShortcut: false)]);
                // @codeCoverageIgnoreEnd
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
            $rules[$attributeRulesIndex] = PropagateOptionsHelper::propagate(
                $this,
                is_iterable($attributeRules) ? $attributeRules : [$attributeRules],
            );
        }

        $this->rules = $rules;
    }

    public function afterInitAttribute(object $object, int $target): void
    {
        if ($this->rules === null) {
            return;
        }

        foreach ($this->rules as $rules) {
            foreach ((is_iterable($rules) ? $rules : [$rules]) as $rule) {
                if ($rule instanceof AfterInitAttributeEventInterface) {
                    $rule->afterInitAttribute($object, $target);
                }
            }
        }
    }

    #[ArrayShape([
        'requirePropertyPath' => 'bool',
        'noRulesWithNoObjectMessage' => 'array',
        'incorrectDataSetTypeMessage' => 'array',
        'incorrectInputMessage' => 'array',
        'noPropertyPathMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array|null',
    ])]
    public function getOptions(): array
    {
        return [
            'noRulesWithNoObjectMessage' => [
                'template' => $this->noRulesWithNoObjectMessage,
                'parameters' => [],
            ],
            'incorrectDataSetTypeMessage' => [
                'template' => $this->incorrectDataSetTypeMessage,
                'parameters' => [],
            ],
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'noPropertyPathMessage' => [
                'template' => $this->getNoPropertyPathMessage(),
                'parameters' => [],
            ],
            'requirePropertyPath' => $this->isPropertyPathRequired(),
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => $this->rules === null ? null : RulesDumper::asArray($this->rules),
        ];
    }

    public function getHandler(): string
    {
        return NestedHandler::class;
    }
}
