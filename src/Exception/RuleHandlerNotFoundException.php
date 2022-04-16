<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\Validator\RuleInterface;

final class RuleHandlerNotFoundException extends RuntimeException
{
    public function __construct(RuleInterface $rule, ?Throwable $previous = null)
    {
        $ruleClassName = get_class($rule);
        $handlerClassName = $rule->getHandlerClassName();

        $message = "Handler was not found for \"{$ruleClassName}\" rule or unresolved \"{$handlerClassName}\" class.";
        parent::__construct($message, 0, $previous);
    }
}
