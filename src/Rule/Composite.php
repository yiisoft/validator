<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Allows to group multiple rules for validation. It's helpful when `skipOnEmpty`, `skipOnError` or `when` options are
 * the same for every rule in the set.
 *
 * For example, with the same `when` closure, without using composite it's specified explicitly for every rule:
 *
 * ```php
 * $when = static function ($value, ValidationContext $context): bool {
 *     return $context->getDataSet()->getPropertyValue('country') === Country::USA;
 * };
 * $rules = [
 *     new Required(when: $when),
 *     new Length(min: 1, max: 50, skipOnEmpty: true, when: $when),
 * ];
 * ```
 *
 * When using composite, specifying it only once will be enough:
 *
 * ```php
 * $rule = new Composite([
 *     new Required(),
 *     new Length(min: 1, max: 50, skipOnEmpty: true),
 *     when: static function ($value, ValidationContext $context): bool {
 *         return $context->getDataSet()->getPropertyValue('country') === Country::USA;
 *     },
 * ]);
 * ```
 *
 * Another use case is reusing this rule group across different places. It's possible by creating own extended class and
 * setting the properties in the constructor:
 *
 * ```php
 * class MyComposite extends Composite
 * {
 *     public function __construct()
 *     {
 *         $this->rules = [
 *             new Required(),
 *             new Length(min: 1, max: 50, skipOnEmpty: true),
 *         ];
 *         $this->when = static function ($value, ValidationContext $context): bool {
 *             return $context->getDataSet()->getPropertyValue('country') === Country::USA;
 *         };
 *     }
 * };
 * ```
 *
 * @see CompositeHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 * @psalm-import-type NormalizedRulesList from RulesNormalizer
 * @psalm-import-type RawRulesList from ValidatorInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Composite implements
    DumpedRuleInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var iterable A set of normalized rules that needs to be grouped.
     *
     * @psalm-var NormalizedRulesList
     */
    protected iterable $rules = [];

    /**
     * @var bool Whether to skip this rule group if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     */
    private bool $skipOnError = false;
    /**
     * @var Closure|null A callable to define a condition for applying this rule group. See {@see WhenInterface}.
     *
     * @psalm-var WhenType
     */
    private Closure|null $when = null;

    /**
     * @param iterable $rules A set of rules that needs to be grouped. They will be normalized using
     * {@see RulesNormalizer}.
     *
     * @psalm-param RawRulesList $rules
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule group if the validated value is empty / not
     * passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule group if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying this rule group. See
     * {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        iterable $rules = [],
        bool|callable|null $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        $this->skipOnEmpty = $skipOnEmpty;
        $this->rules = RulesNormalizer::normalizeList($rules);
        $this->skipOnError = $skipOnError;
        $this->when = $when;
    }

    public function getName(): string
    {
        return self::class;
    }

    #[ArrayShape([
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array',
    ])]
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => $this->dumpRulesAsArray(),
        ];
    }

    /**
     * Gets a set of normalized rules that needs to be grouped.
     *
     * @return iterable A set of rules.
     *
     * @psalm-return NormalizedRulesList
     *
     * @see $rules
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    final public function getHandler(): string
    {
        return CompositeHandler::class;
    }

    public function afterInitAttribute(object $object): void
    {
        foreach ($this->getRules() as $rule) {
            if ($rule instanceof AfterInitAttributeEventInterface) {
                $rule->afterInitAttribute($object);
            }
        }
    }

    /**
     * Dumps grouped {@see $rules} to array.
     *
     * @return array The array of rules with their options.
     */
    final protected function dumpRulesAsArray(): array
    {
        return RulesDumper::asArray($this->getRules());
    }
}
