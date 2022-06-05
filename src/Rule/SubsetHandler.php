<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

final class SubsetHandler implements RuleHandlerInterface
{
    private ?FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Subset) {
            throw new UnexpectedRuleException(Subset::class, $rule);
        }

        $result = new Result();

        if (!is_iterable($value)) {
            $formattedMessage = $this->formatter->format(
                $rule->getIterableMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $rule->getValues(), $rule->isStrict())) {
            $values = $rule->getValues() instanceof Traversable
                ? iterator_to_array($rule->getValues())
                : $rule->getValues();
            $valuesString = '"' . implode('", "', $values) . '"';

            $formattedMessage = $this->formatter->format(
                $rule->getSubsetMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value, 'values' => $valuesString]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
