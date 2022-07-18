<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class CallbackHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Callback) {
            throw new UnexpectedRuleException(Callback::class, $rule);
        }

        $callback = $rule->getCallback();
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $formattedMessage = $this->formatter->format(
                $error->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage, $error->getValuePath());
        }

        return $result;
    }
}
