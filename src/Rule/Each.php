<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\ValidationContext;

/**
 * Each validator validates an array by checking each of its elements against a set of rules
 */
class Each extends Rule
{
    use HasValidationErrorMessage;

    private Rules $rules;

    private string $incorrectInputMessage = 'Value should be array or iterable';
    private string $message = '{error} {value} given.';

    public static function rule(Rules $rules): self
    {
        $rule = new self();
        $rule->rules = $rules;
        return $rule;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($this->incorrectInputMessage);
            return $result;
        }

        foreach ($value as $item) {
            $itemResult = $this->rules->validate($item, $context);
            if ($itemResult->isValid() === false) {
                foreach ($itemResult->getErrors() as $error) {
                    $result->addError(
                        $this->formatMessage(
                            $this->message,
                            [
                                'error' => $error,
                                'value' => $item,
                            ]
                        )
                    );
                }
            }
        }

        return $result;
    }

    public function incorrectInputMessage(string $message): self
    {
        $new = clone $this;
        $new->incorrectInputMessage = $message;
        return $new;
    }

    public function getOptions(): array
    {
        return $this->rules->asArray();
    }
}
