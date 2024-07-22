<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;

use function array_slice;
use function implode;
use function is_string;

/**
 * Validation result that is used by both {@see ValidatorInterface} and {@see RuleHandlerInterface}.
 */
final class Result
{
    /**
     * @var Error[] Validation errors.
     * @psalm-var list<Error>
     */
    private array $errors = [];

    /**
     * Whether result doesn't have any validation errors.
     *
     * @return bool Whether result is valid.
     */
    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * Whether attribute specified doesn't have any validation errors.
     *
     * @param string $attribute Attribute name.
     *
     * @return bool Whether attribute is valid.
     */
    public function isAttributeValid(string $attribute): bool
    {
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $attribute) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return Error[] Validation errors.
     * @psalm-return list<Error>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors messages as an array of strings.
     *
     * @return string[] Array messages as strings.
     * @psalm-return list<string>
     */
    public function getErrorMessages(): array
    {
        return array_map(static fn (Error $error): string => $error->getMessage(), $this->errors);
    }

    /**
     * Get arrays of error messages indexed by attribute path.
     * Each key is a dot-separated attribute path.
     * Each value is an array of error message strings.
     *
     * @param string $separator Attribute path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Arrays of error messages indexed by attribute path.
     *
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorMessagesIndexedByPath(string $separator = '.', ?string $escape = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath($escape));
            $errors[$stringValuePath][] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get strings of the first error messages for each attribute path.
     * Each key is a dot-separated attribute path.
     * Each value is the first error message string for this path.
     *
     * @param string $separator Attribute path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Strings of error messages indexed by attribute path.
     *
     * @psalm-return array<string, string>
     */
    public function getFirstErrorMessagesIndexedByPath(string $separator = '.', ?string $escape = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath($escape));
            $errors[$stringValuePath] ??= $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get arrays of error messages indexed by attribute name.
     *
     * @throws InvalidArgumentException If top level attribute has a non-string type.
     *
     * @return array Arrays of error messages indexed by attribute name.
     *
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorMessagesIndexedByAttribute(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            if (!is_string($key)) {
                throw new InvalidArgumentException('Top level attributes can only have string type.');
            }

            $errors[$key][] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get arrays of the first error messages for each attribute name.
     *
     * @throws InvalidArgumentException If top level attribute has a non-string type.
     *
     * @return array Strings of error messages indexed by attribute name.
     *
     * @psalm-return array<string, string>
     */
    public function getFirstErrorMessagesIndexedByAttribute(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            if (!is_string($key)) {
                throw new InvalidArgumentException('Top level attributes can only have string type.');
            }

            $errors[$key] ??= $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get an array of error objects for the attribute specified.
     *
     * @param string $attribute Attribute name.
     *
     * @return Error[] Array of error objects.
     *
     * @psalm-return list<Error>
     */
    public function getAttributeErrors(string $attribute): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $attribute) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * Get an array of error messages for the attribute specified.
     *
     * @return string[] Error messages.
     *
     * @psalm-return list<string>
     */
    public function getAttributeErrorMessages(string $attribute): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $attribute) {
                $errors[] = $error->getMessage();
            }
        }
        return $errors;
    }

    /**
     * Get arrays of error messages for the attribute specified indexed by attribute path.
     * Each key is a dot-separated attribute path.
     * Each value is an array of error message strings.
     *
     * @param string $attribute Attribute name.
     * @param string $separator Attribute path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Arrays of error messages for the attribute specified indexed by attribute path.
     *
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getAttributeErrorMessagesIndexedByPath(
        string $attribute,
        string $separator = '.',
        ?string $escape = '.',
    ): array {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem !== $attribute) {
                continue;
            }

            $valuePath = implode($separator, array_slice($error->getValuePath($escape), 1));
            $errors[$valuePath][] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get common error messages that are not attached to any attribute.
     *
     * @return string[] Error messages.
     *
     * @psalm-return list<string>
     */
    public function getCommonErrorMessages(): array
    {
        return $this->getAttributeErrorMessages('');
    }

    /**
     * Add an error.
     *
     * @param string $message The raw validation error message. See {@see Error::$message}.
     * @param array $parameters Parameters used for {@see $message} translation - a mapping between parameter
     * names and values. See {@see Error::$parameters}.
     *
     * @psalm-param array<string,scalar|null> $parameters
     *
     * @param array $valuePath A sequence of keys determining where a value caused the validation
     * error is located within a nested structure. See {@see Error::$valuePath}.
     *
     * @psalm-param list<int|string> $valuePath
     *
     * @return $this Same instance of result.
     */
    public function addError(string $message, array $parameters = [], array $valuePath = []): self
    {
        $this->errors[] = new Error($message, $parameters, $valuePath);
        return $this;
    }

    /**
     * Add an error, the message of which does not require translation, but should be formatted.
     *
     * @see addError()
     *
     * @psalm-param array<string,scalar|null> $parameters
     * @psalm-param list<int|string> $valuePath
     *
     * @return $this Same instance of result.
     */
    public function addErrorWithFormatOnly(string $message, array $parameters = [], array $valuePath = []): self
    {
        $this->errors[] = new Error($message, $parameters, $valuePath, Error::MESSAGE_FORMAT);
        return $this;
    }

    /**
     * Add an error, the message of which does not require any post-processing.
     *
     * @see addError()
     *
     * @psalm-param array<string,scalar|null> $parameters
     * @psalm-param list<int|string> $valuePath
     *
     * @return $this Same instance of result.
     */
    public function addErrorWithoutPostProcessing(string $message, array $parameters = [], array $valuePath = []): self
    {
        $this->errors[] = new Error($message, $parameters, $valuePath, Error::MESSAGE_NONE);
        return $this;
    }

    /**
     * Merges other validation results into the current one.
     *
     * @param Result ...$results Other results for merging.
     * @return $this Same instance of result.
     */
    public function add(self ...$results): self
    {
        $appendErrors = [];
        foreach ($results as $result) {
            $appendErrors[] = $result->getErrors();
        }
        $this->errors = array_merge($this->errors, ...$appendErrors);
        return $this;
    }
}
