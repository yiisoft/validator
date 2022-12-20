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
     * @return RuleHandlerInterface A corresponding rule handler instance.
     *
     * @throws RuleHandlerNotFoundException if a rule handler instance was not found.
     * @throws RuleHandlerInterfaceNotImplementedException if a found instance is not a valid rule handler.
     */
    public function resolve(string $name): RuleHandlerInterface;
}
