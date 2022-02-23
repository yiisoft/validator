<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

/**
 * GroupRule validates a single value for a set of custom rules
 */
abstract class GroupRule extends Rule
{
    public function __construct(
        protected string $message = 'This value is not a valid.',

        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        if (!$this->getRuleSet()->validate($value, $context)->isValid()) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    /**
     * Return custom rules set
     */
    abstract protected function getRuleSet(): RuleSet;

    public function getOptions(): array
    {
        return $this->getRuleSet()->asArray();
    }
}
