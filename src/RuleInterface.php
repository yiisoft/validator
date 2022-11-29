<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * A main interface for rules to implement. If you want to include a rule options in addition to a rule name during
 * conversion to array, use extended version of it - {@see SerializableRuleInterface}.
 */
interface RuleInterface
{
    /**
     * Gets the name of a rule used during conversion to array. The main usage of it is identification, mainly on
     * the frontend for implementing client-side validation but other applications are possible. This is explicitly
     * specified for optimization and readability purposes.
     *
     * All packages' rule names use class name written in camelCase, so for `AtLeast` rule the name will be `atLeast`
     * and so on. For custom rules you can choose different naming scheme because it doesn't affect logic in any way.
     *
     * @see RulesDumper
     */
    public function getName(): string;

    /**
     * A matching handler class name used for processing this rule.
     *
     * While not required, it's recommended to use rule class name with "Handler" suffix, so for `AtLeast` rule class
     * name the handler class name will be `AtLeastHandler` and so on.
     *
     * All packages handlers are stored within the same namespace as rules, but this is not a strict requirement.
     */
    public function getHandlerClassName(): string;
}
