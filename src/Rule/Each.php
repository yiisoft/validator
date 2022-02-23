<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

/**
 * Each validator validates an array by checking each of its elements against a set of rules
 */
final class Each extends Rule
{
    public function __construct(
        private RuleSet $ruleSet,
        private string $incorrectInputMessage = 'Value should be array or iterable.',
        private string $message = '{error} {value} given.',

        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null,
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($this->incorrectInputMessage);
            return $result;
        }

        foreach ($value as $index => $item) {
            $itemResult = $this->ruleSet->validate($item, $context);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                if (!is_array($item)) {
                    $errorKey = [$index];
                    $formatMessage = true;
                } else {
                    $errorKey = [$index, ...$error->getValuePath()];
                    $formatMessage = false;
                }

                $message = !$formatMessage ? $error->getMessage() : $this->formatMessage($this->message, [
                    'error' => $error->getMessage(),
                    'value' => $item,
                ]);

                $result->addError($message, $errorKey);
            }
        }

        return $result;
    }

    public function getOptions(): array
    {
        return $this->ruleSet->asArray();
    }
}
