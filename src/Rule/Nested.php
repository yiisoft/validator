<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rules;

/**
 * Each validator validates an array by checking each of its elements against a set of rules
 */
class Nested extends Rule
{
    use HasValidationErrorMessage;

    /**
     * @var Rule[][]
     */
    private iterable $rules;

    private string $incorrectInputMessage = 'Value should be array or iterable';
    private string $message = '{error} {value} given.';

    public function __construct(iterable $rules)
    {
        $this->rules = $rules;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        if (!is_iterable($value)) {
            $result->addError($this->incorrectInputMessage);
            return $result;
        }

        foreach ($this->rules as $valuePath => $rules) {
            $validatedValue = ArrayHelper::getValueByPath($value, $valuePath);
            $aggregateRule = new Rules($rules);
            $itemResult = $aggregateRule->validate($validatedValue);
            if ($itemResult->isValid() === false) {
                foreach ($itemResult->getErrors() as $error) {
                    $result->addError(
                        $this->translateMessage(
                            $this->message,
                            [
                                'error' => $error,
                                'value' => $validatedValue,
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
