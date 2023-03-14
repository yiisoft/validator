<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Helper\PropagateOptionsHelper;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Allows to define a set of rules for validating each element of an iterable.
 *
 * An example for simple iterable that can be used to validate RGB color:
 *
 * ```php
 * $rules = [
 *     new Count(3), // Not required for using with `Each`.
 *     new Each([
 *         new Integer(min: 0, max: 255),
 *         // More rules can be added here.
 *     ]),
 * ];
 * ```
 *
 * When paired with {@see Nested} rule, it allows validation of related data:
 *
 * ```php
 * $coordinateRules = [new Number(min: -10, max: 10)];
 * $rule = new Each([
 *     new Nested([
 *         'coordinates.x' => $coordinateRules,
 *         'coordinates.y' => $coordinateRules,
 *     ]),
 * ]);
 * ```
 *
 * It's also possible to use DTO objects with PHP attributes, see {@see ObjectDataSet} documentation and guide for
 * details.
 *
 * Supports propagation of options (see {@see PropagateOptionsHelper::propagate()} for available options and
 * requirements).
 *
 * @see EachHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type RawRules from ValidatorInterface
 * @psalm-import-type NormalizedRulesMap from RulesNormalizer
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Each implements
    RuleWithOptionsInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface,
    PropagateOptionsInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var array Normalized rules to apply for each element of the validated iterable.
     * @psalm-var NormalizedRulesMap
     */
    private array $rules;

    /**
     * @param callable|iterable|object|string $rules Rules to apply for each element of the validated iterable.
     * They will be normalized using {@see RulesNormalizer}.
     * @psalm-param RawRules $rules
     *
     * @param string $incorrectInputMessage Error message used when validation fails because the validated value is not
     * an iterable.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectInputKeyMessage Error message used when validation fails because the validated iterable
     * contains invalid keys. Only integer and string keys are allowed.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the iterable key being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this `Each` rule with all defined {@see $rules} if the
     * validated value is empty / not passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this `Each` rule with all defined {@see $rules} if any of the previous
     * rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying this `Each` rule with all defined
     * {@see $rules}. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        callable|iterable|object|string $rules = [],
        private string $incorrectInputMessage = 'Value must be array or iterable.',
        private string $incorrectInputKeyMessage = 'Every iterable key must have an integer or a string type.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->rules = RulesNormalizer::normalize($rules);
    }

    public function getName(): string
    {
        return 'each';
    }

    public function propagateOptions(): void
    {
        foreach ($this->rules as $key => $attributeRules) {
            $this->rules[$key] = PropagateOptionsHelper::propagate($this, $attributeRules);
        }
    }

    /**
     * Gets a set of rules that needs to be applied to each element of the validated iterable.
     *
     * @return array A set of rules.
     *
     * @psalm-return NormalizedRulesMap
     *
     * @see $rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Gets error message used when validation fails because the validated value is not an iterable.
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
     * Error message used when validation fails because the validated iterable contains invalid keys.
     *
     * @return string Error message / template.
     *
     * @see $incorrectInputKeyMessage
     */
    public function getIncorrectInputKeyMessage(): string
    {
        return $this->incorrectInputKeyMessage;
    }

    #[ArrayShape([
        'incorrectInputMessage' => 'array',
        'incorrectInputKeyMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array',
    ])]
    public function getOptions(): array
    {
        return [
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'incorrectInputKeyMessage' => [
                'template' => $this->incorrectInputKeyMessage,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => RulesDumper::asArray($this->rules),
        ];
    }

    public function getHandler(): string
    {
        return EachHandler::class;
    }

    public function afterInitAttribute(object $object): void
    {
        foreach ($this->rules as $attributeRules) {
            foreach ($attributeRules as $rule) {
                if ($rule instanceof AfterInitAttributeEventInterface) {
                    $rule->afterInitAttribute($object);
                }
            }
        }
    }
}
