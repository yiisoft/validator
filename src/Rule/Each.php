<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rules;

/**
 * Each validator validates an array by checking each of its elements against a set of rules
 */
class Each extends Rule
{
    use HasValidationErrorMessage;

    private Rules $rules;

    private string $incorrectInputMessage = 'Value should be array or iterable';
    private string $message = ' {value} given.';

    public function __construct(Rules $rules)
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

        foreach ($value as $item) {
            $itemResult = $this->rules->validate($item, $dataSet);
            if ($itemResult->isValid() === false) {
                foreach ($itemResult->getErrors() as $error) {
                    $result->addError(
                        $error->message . $this->message,
                        array_merge(['value' => $item], $error->params)
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

    public function getOptions(?TranslatorInterface $translator = null): array
    {
        return $this->rules->asArray($translator);
    }
}
