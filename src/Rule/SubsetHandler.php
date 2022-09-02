<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Traversable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class SubsetHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Subset) {
            throw new UnexpectedRuleException(Subset::class, $rule);
        }

        $result = new Result();

        if (!is_iterable($value)) {
            $formattedMessage = $this->translator->translate(
                $rule->getIterableMessage(),
                ['attribute' => $context->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }

        if (!ArrayHelper::isSubset($value, $rule->getValues(), $rule->isStrict())) {
            $values = $rule->getValues() instanceof Traversable
                ? iterator_to_array($rule->getValues())
                : $rule->getValues();
            $valuesString = '"' . implode('", "', $values) . '"';

            $formattedMessage = $this->translator->translate(
                $rule->getSubsetMessage(),
                ['attribute' => $context->getAttribute(), 'value' => $value, 'values' => $valuesString]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
