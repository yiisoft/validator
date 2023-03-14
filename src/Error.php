<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Validator\Rule\Callback;

/**
 * A class representing validation error. It's added in a rule handler or via {@see Callback} rule to the {@see Result}
 * to form the complete list of errors for a single validation.
 */
final class Error
{
    /**
     * @param string $message The raw validation error message. Can be a simple text or a template with placeholders enclosed
     * in curly braces (`{}`). In the end of the validation it will be translated using configured translator.
     * {@see SimpleMessageFormatter} is usually enough, but for more complex translations
     * {@see IntlMessageFormatter} can be used (requires "intl" PHP extension). Examples:
     *
     * - `'The value must be a string.'` - simple text, works with both {@see SimpleMessageFormatter} and
     * {@see IntlMessageFormatter}.
     * - `'The value must be "{true}".'` - simple substitution, works with both formatters.
     * - `'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' - plural form,
     * works with both formatters.
     * - `'You are {position, selectordinal, one {#st} two {#nd} few {#rd} other {#th}} in the queue.'` - more
     * complex syntax, works only with {@see IntlMessageFormatter}, requires "intl".
     *
     * @link https://www.php.net/manual/en/book.intl.php
     *
     * @param array $parameters  Parameters used for {@see $message} translation - a mapping between parameter
     * names and values. Note that only scalar or `null` values are allowed.
     *
     * @link https://www.php.net/manual/ru/function.is-scalar.php
     * @psalm-param array<string, scalar|null> $parameters
     *
     * @param array $valuePath A sequence of keys determining where a value caused the validation error is located
     * within a nested structure. Examples of different value paths:
     *
     * ```php
     * $data = [
     *     [
     *         1,
     *         'text', // The value path is [0, 1].
     *     ],
     *     'post' => [
     *         'title' => 'Yii3 Overview 3', // The value path is ['post', 'title'].
     *         'files' => [
     *             [
     *                 'url' => '...', // The value path is ['post', 'files', 0, 'url'].
     *             ],
     *         ],
     *     ],
     * ];
     * ```
     *
     * A value without nested structure won't have a path at all (it will be an empty array).
     * @psalm-param list<int|string> $valuePath
     */
    public function __construct(
        private string $message,
        private array $parameters = [],
        private array $valuePath = [],
    ) {
    }

    /**
     * A getter for {@see $message} property. Returns raw (non-translated) validation error message.
     *
     * @return string A simple text or a template used for translation.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * A getter for {@see $parameters} property. Returns parameters used for {@see $message} translation.
     *
     * @return array A mapping between parameter names and values.
     * @psalm-return array<string, scalar|null>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * A getter for {@see $valuePath} property. Returns a sequence of keys determining where a value caused the
     * validation error is located within a nested structure.
     *
     * @param bool $escape Whether to escape a dot (`'.'`, used as a separator in a string representation) and asterisk
     * (`'*``, used as a {@see Each} rule shortcut) char with a backslash char (`'\'`).
     *
     * @return array A list of keys for nested structures or an empty array otherwise.
     * @psalm-return list<int|string>
     */
    public function getValuePath(bool $escape = false): array
    {
        if ($escape === false) {
            return $this->valuePath;
        }

        return array_map(
            static fn ($key): string => str_replace(['.', '*'], ['\\' . '.', '\\' . '*'], (string) $key),
            $this->valuePath,
        );
    }
}
