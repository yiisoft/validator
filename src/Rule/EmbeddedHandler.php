<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class EmbeddedHandler implements RuleHandlerInterface
{
    public function __construct(
        private ?FormatterInterface $formatter = null
    ) {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Embedded) {
            throw new UnexpectedRuleException(Embedded::class, $rule);
        }

        if (!is_object($value)) {
            $formattedMessage = $this->formatter->format(
                'Value should be an object, {type} given.',
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                    'type' => get_debug_type($value)
                ]
            );
            return (new Result())->addError($formattedMessage);
        }

        return $context->getValidator()->validate($value);
    }
}
