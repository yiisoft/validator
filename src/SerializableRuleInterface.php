<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An extended version of {@see RuleInterface} which allows exporting a rule options in addition to a rule name. It's
 * useful for passing to frontend for further identification and implementing client-side validation. If you don't need
 * that (for example for REST API), use {@see RuleInterface} instead.
 */
interface SerializableRuleInterface extends RuleInterface
{
    /**
     * Gets a rule options as associative array. It's used for identification on the frontend with further implementing
     * of client-side validation. Usually it's just a mapping between rule property names and values.
     *
     * For a example, for this rule:
     *
     * ```php
     * new SomeRule(property1: 'value1', property2: 'value2');
     * ```
     *
     * the options will be:
     *
     * ```php
     * [
     *     'property1' => $this->>property1,
     *     'property2' => $this->>property2,
     *     // ...
     * ];
     * ```
     *
     * Sometimes it's helpful to include dynamic options as well:
     *
     * ```php
     * [
     *     // ...
     *     'dynamicOption' => $this->getDynamicOption(), // Dynamic option is not necessarily a rule class property.
     *     // ...
     * ];
     * ```
     *
     * For messages the value is a nested array with the following structure (below is a result of the method call):
     *
     * ```php
     * 'message' => [ // Array is used with no parameters too.
     *     'template => 'A message without parameters.',
     *     'parameters' => [], // Explicitly specified even for empty parameters.
     * ],
     * 'anotherMessage' => [
     *     'template' => 'A message with {parameter}.', // Handler dependent parameters
     *     'parameters' => ['property4' => $this->property4], // Property dependent parameters
     * ],
     * 'wrongMessage' => 'Wrong message.' // This is possible, but wrong. Use example above for consistent structure.
     * ```
     *
     * Note that the values that can't be serialized to frontend such as callable must be excluded because they will be
     * useless on frontend. No exception will be thrown, so it's on the conscience of developer.
     *
     * @return array A rule options.
     */
    public function getOptions(): array;
}
