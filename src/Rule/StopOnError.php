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
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Applies to a set of rules, runs validation for each one of them in the order they are defined and stops at the rule
 * where validation failed.
 *
 * An example of usage:
 *
 * ```php
 * $rule = new StopOnError([
 *      new Required(), // Let's say there is an error.
 *      new Length(min: 3), // Then this rule will be skipped.
 *      new MyCustomRule(), // This rule will be skipped too.
 * ]);
 * ```
 *
 * When using with other rules, conditional validation options, such as {@see StopOnError::$skipOnError} will be applied
 * to the whole group of {@see StopOnError::$rules}.
 *
 * Not to be confused with skipping each rule individually, there is a separate functionality for that, see
 * {@see SkipOnErrorInterface}.
 *
 * There is a similar rule that stops on successful validation - {@see Any}.
 *
 * @see StopOnErrorHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 * @psalm-import-type NormalizedRulesList from RulesNormalizer
 * @psalm-import-type RawRulesList from ValidatorInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class StopOnError implements
    RuleWithOptionsInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var iterable A set of normalized rules that needs to be run.
     *
     * @psalm-var NormalizedRulesList
     */
    private iterable $rules = [];

    /**
     * @param iterable $rules A set of rules for running the validation. They will be normalized during initialization
     * using {@see RulesNormalizer}.
     *
     * @psalm-param RawRulesList $rules
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this `StopOnError` rule with all defined {@see $rules} if
     * the validated value is empty / not passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this `StopOnError` rule with all defined {@see $rules} if any of the
     * previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying this `StopOnError` rule with all defined
     * {@see $rules}. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        iterable $rules,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->rules = RulesNormalizer::normalizeList($rules);
    }

    public function getName(): string
    {
        return 'stopOnError';
    }

    /**
     * Gets a set of rules for running the validation.
     *
     * @return iterable A set of rules.
     *
     * @psalm-return NormalizedRulesList
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
            'rules' => $this->dumpRulesAsArray(),
        ];
    }

    public function getHandler(): string
    {
        return StopOnErrorHandler::class;
    }

    public function afterInitAttribute(object $object): void
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof AfterInitAttributeEventInterface) {
                $rule->afterInitAttribute($object);
            }
        }
    }

    /**
     * Dumps defined {@see $rules} to array.
     *
     * @return array The array of rules with their options.
     */
    private function dumpRulesAsArray(): array
    {
        return RulesDumper::asArray($this->getRules());
    }
}
