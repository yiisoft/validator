<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

/**
 * GroupRule validates a single value for a set of custom rules
 */
abstract class GroupRule extends Rule
{
    use HasValidationErrorMessage;

    protected string $message = 'This value is not a valid.';

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!$this->getRuleSet()->validate($value, $context)->isValid()) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    /**
     * Return custom rules set
     *
     * @return RuleSet
     */
    abstract protected function getRuleSet(): RuleSet;

    public function getOptions(): array
    {
        return $this->getRuleSet()->asArray();
    }
}
