<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface allowing to resolve a rule handler class name to a corresponding rule handler instance.
 */
interface RuleHandlerResolverInterface
{
    /**
     * Resolves a rule handler class name to a corresponding rule handler instance.
     *
     * @param string $className A rule handler class name ({@see RuleInterface}).
     *
     * @return RuleHandlerInterface A corresponding rule handler instance.
     */
    public function resolve(string $className): RuleHandlerInterface;
}
