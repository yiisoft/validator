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
use Yiisoft\Validator\PropagateOptionsInterface;
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
final class Nested implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface,
    PropagateOptionsInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;
    use SkipOnEmptyTrait;

    private const SEPARATOR = '.';
    private const EACH_SHORTCUT = '*';

    /**
     * @var iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|null
     */
    private ?iterable $rules;

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

    /**
     * @return iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|null
     */
    public function getRules(): ?iterable
    {
        return $this->rules;
    }

    public function getPropertyVisibility(): int
    {
        return $this->propertyVisibility;
    }

    /**
     * @return bool
     */
    public function getRequirePropertyPath(): bool
    {
        return $this->requirePropertyPath;
    }

    /**
     * @return string
     */
    public function getNoPropertyPathMessage(): string
    {
        return $this->noPropertyPathMessage;
    }

    /**
     * @param class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|RulesProviderInterface|null $source
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

        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        if (self::checkRules($rules)) {
            $message = sprintf('Each rule should be an instance of %s.', RuleInterface::class);
            throw new InvalidArgumentException($message);
        }

        $this->rules = $rules;

        if ($this->normalizeRules) {
            $this->normalizeRules();
        }

        if ($this->propagateOptions) {
            $this->propagateOptions();
        }
    }

    private static function checkRules($rules): bool
    {
        return array_reduce(
            $rules,
            function (bool $carry, $rule) {
                return $carry || (is_array($rule) ? self::checkRules($rule) : !$rule instanceof RuleInterface);
            },
            false
        );
    }

    private function normalizeRules(): void
    {
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
        foreach ($this->rules as $attributeRulesIndex => $attributeRules) {
            foreach ($attributeRules as $index => $attributeRule) {
                $attributeRule = $attributeRule->skipOnEmpty($this->skipOnEmpty);
                $attributeRule = $attributeRule->skipOnError($this->skipOnError);

                $this->rules[$attributeRulesIndex][$index] = $attributeRule;

                if ($attributeRule instanceof PropagateOptionsInterface) {
                    $attributeRule->propagateOptions();
                }
            }
        }
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
