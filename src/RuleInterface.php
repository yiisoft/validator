<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Helper\RulesDumper;

/**
 * A main interface for rules to implement. A rule contains a set of constraint configuration options to apply when
 * validating data. If you want to include a rule options in addition to a rule name during conversion to array, use
 * extended version of it - {@see RuleWithOptionsInterface}.
 */
interface RuleInterface
{
    /**
     * Returns the name of a rule used during conversion to array. It's used for identification on the frontend with
     * further implementing of client-side validation. This is explicitly specified for optimization and readability
     * purposes.
     *
     * All packages' rule names use class name written in camelCase, so for `AtLeast` rule the name will be `atLeast.
     * For custom rules you can choose different naming scheme because it doesn't affect logic in any way.
     *
     * @see RulesDumper
     *
     * @return string A rule name.
     */
    public function getName(): string;

    /**
     * A matching handler class name used for processing this rule.
     *
     * While not required, it's recommended to use rule class name with "Handler" suffix, so for `AtLeast` rule class
     * name the handler class name will be `AtLeastHandler` and so on.
     *
     * All packages handlers are stored within the same namespace as rules, but this is not a strict requirement.
     *
     * @return string A rule handler class name.
     */
    public function getHandlerClassName(): string;
}
