<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * A main interface for rules to implement. A rule contains a set of constraint configuration options to apply when
 * validating data. If you want to include a rule options and customize its name during conversion to array, use
 * extended version of it - {@see DumpedRuleInterface}.
 */
interface RuleInterface
{
    /**
     * A matching handler name or an instance used for processing this rule.
     *
     * While not required, for naming of handlers' classes it's recommended to use a rule class name with "Handler"
     * suffix, so for `AtLeast` rule class name the handler class name will be `AtLeastHandler` and so on.
     *
     * All packages handlers are stored within the same namespace as rules, but this is not a strict requirement.
     *
     * @return RuleHandlerInterface|string A rule handler name (for example `my-handler`) or an instance (for example
     * `new MyRuleHandler()`).
     */
    public function getHandler(): string|RuleHandlerInterface;
}
