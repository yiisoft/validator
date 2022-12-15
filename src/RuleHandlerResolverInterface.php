<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface for dependency injection containers to implement. Allows to resolve a rule handler by a given rule class
 * name.
 */
interface RuleHandlerResolverInterface
{
    /**
     * Resolves a rule handler by a rule class name.
     *
     * @param string $ruleClassName A rule class name ({@see RuleInterface}).
     *
     * @return RuleHandlerInterface A rule handler instance.
     */
    public function resolve(string $ruleClassName): RuleHandlerInterface;
}
