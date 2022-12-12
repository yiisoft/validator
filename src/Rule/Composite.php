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
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Allows to combine and validate multiple rules.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Composite implements
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
     * @var iterable<int, RuleInterface>
     */
    protected iterable $rules = [];

    /**
     * @var bool|callable|null
     */
    protected $skipOnEmpty = null;

    protected bool $skipOnError = false;

    /**
     * @psalm-var WhenType
     */
    protected Closure|null $when = null;

    private ?RulesDumper $rulesDumper = null;

    /**
     * @param iterable<Closure|RuleInterface> $rules
     *
     * @psalm-param WhenType $when
     */
    public function __construct(
        iterable $rules = [],
        bool|callable|null $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        $this->rules = RulesNormalizer::normalizeList($rules);
        $this->skipOnEmpty = $skipOnEmpty;
        $this->skipOnError = $skipOnError;
        $this->when = $when;
    }

    public function getName(): string
    {
        return 'composite';
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
     * @return iterable<int, RuleInterface>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    final public function getHandlerClassName(): string
    {
        return CompositeHandler::class;
    }

    public function afterInitAttribute(object $object, int $target): void
    {
        foreach ($this->getRules() as $rule) {
            if ($rule instanceof AfterInitAttributeEventInterface) {
                $rule->afterInitAttribute($object, $target);
            }
        }
    }

    final protected function dumpRulesAsArray(): array
    {
        return $this->getRulesDumper()->asArray($this->getRules());
    }

    private function getRulesDumper(): RulesDumper
    {
        if ($this->rulesDumper === null) {
            $this->rulesDumper = new RulesDumper();
        }

        return $this->rulesDumper;
    }
}
