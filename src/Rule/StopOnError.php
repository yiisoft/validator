<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Applies to a set of rules, runs validation for each one of them in the order they are defined and stops at the rule
 * where validation failed. In particular, it can be useful for preventing the heavy operations to increase performance.
 * It can be set like that for a group of ordered rules:
 *
 * ```php
 * $rule = new StopOnError([
 *      new HasLength(min: 3),
 *      // This operation executes DB query and thus heavier. It's preferable not to call it if the previous rule did
 *      not pass the validation.
 *      new ExistsInDatabase(),
 * ]);
 * ```
 *
 * Not to be confused with skipping, there is a separate functionality for that, see {@see SkipOnErrorInterface}.
 *
 * @see StopOnErrorHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class StopOnError implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param iterable $rules A set of rules for running the validation. Note that they are not normalized.
     * @psalm-param iterable<RuleInterface> $rules
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this `StopOnError` rule with all defined {@see $rules} if
     * the validated value is empty / not passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this `StopOnError` rule with all defined {@see $rules} if any of the
     * previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying this `StopOnError` rule with all defined
     * {@see $rules}. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private iterable $rules,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
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
     * @psalm-return iterable<RuleInterface>
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

    public function afterInitAttribute(object $object, int $target): void
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof AfterInitAttributeEventInterface) {
                $rule->afterInitAttribute($object, $target);
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
