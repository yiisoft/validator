<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;

/**
 * An interface allowing to resolve a rule handler name to a corresponding rule handler instance.
 */
interface RuleHandlerResolverInterface
{
    /**
     * Resolves a rule handler name to a corresponding rule handler instance.
     *
     * @param string $name A rule handler name ({@see RuleInterface}).
     *
     * @throws RuleHandlerNotFoundException If a rule handler instance was not found.
     * @throws RuleHandlerInterfaceNotImplementedException If a found instance is not a valid rule handler.
     *
     * @return RuleHandlerInterface A corresponding rule handler instance.
     */
    public function resolve(string $name): RuleHandlerInterface;
}
