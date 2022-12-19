<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

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
     */
    public function resolve(string $name): RuleHandlerInterface;
}
