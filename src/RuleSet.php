<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Callback\Callback;
use function get_class;
use function gettype;
use function is_callable;

/**
 * Rule set represents multiple rules for a single value.
 */
final class RuleSet
{
    public const PARAMETER_PREVIOUS_RULES_ERRORED = 'previousRulesErrored';

    /**
     * @var ParametrizedRuleInterface[]
     */
    private array $rules = [];
    private RuleValidatorStorage $storage;

    public function __construct(RuleValidatorStorage $storage, iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->add($rule);
        }
        $this->storage = $storage;
    }

    /**
     * @param callable|ParametrizedRuleInterface $rule
     */
    public function add($rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, object $config, ValidationContext $context = null): Result
    {
        $context = $context ?? new ValidationContext();

        $compoundResult = new Result();
        foreach ($this->rules as $rule) {
            $validator = $this->storage->getValidator(get_class($rule));
            $ruleResult = $validator->validate($value, $rule, $context);
            if ($ruleResult->isValid()) {
                continue;
            }

            $context->setParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED, true);

            foreach ($ruleResult->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getValuePath());
            }
        }
        return $compoundResult;
    }

    private function normalizeRule($rule): ParametrizedRuleInterface
    {
        if (is_callable($rule)) {
            return new Callback($rule);
        }

        if (!$rule instanceof ParametrizedRuleInterface) {
            throw new InvalidArgumentException(sprintf(
                'Rule should be either an instance of %s or a callable, %s given.',
                ParametrizedRuleInterface::class,
                gettype($rule)
            ));
        }

        return $rule;
    }

    /**
     * Return rules as array.
     *
     * @return array
     */
    public function asArray(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof ParametrizedRuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            } else {
                $arrayOfRules[] = [get_class($rule)];
            }
        }
        return $arrayOfRules;
    }
}
