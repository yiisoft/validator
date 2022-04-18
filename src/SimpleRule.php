<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

abstract class SimpleRule implements RuleInterface
{
    abstract public function validate(mixed $value, ?ValidationContext $context = null): Result;

    final public function getHandlerClassName(): string
    {
        return SimpleRuleHandler::class;
    }
}
