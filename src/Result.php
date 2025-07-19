<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use Stringable;

use function array_slice;
use function count;
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
     * Whether property specified doesn't have any validation errors.
     *
     * @param string $property Property name.
     *
     * @return bool Whether property is valid.
     */
    public function isPropertyValid(string $property): bool
    {
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $property) {
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
     * Get arrays of error messages indexed by property path.
     * Each key is a dot-separated property path.
     * Each value is an array of error message strings.
     *
     * @param string $separator Property path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Arrays of error messages indexed by property path.
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
     * Get strings of the first error messages for each property path.
     * Each key is a dot-separated property path.
     * Each value is the first error message string for this path.
     *
     * @param string $separator Property path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Strings of error messages indexed by property path.
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
     * Get arrays of error messages indexed by property name.
     *
     * @throws InvalidArgumentException If top level property has a non-string type.
     *
     * @return array Arrays of error messages indexed by property name.
     *
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorMessagesIndexedByProperty(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            if (!is_string($key)) {
                throw new InvalidArgumentException('Top level properties can only have string type.');
            }

            $errors[$key][] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get arrays of the first error messages for each property name.
     *
     * @throws InvalidArgumentException If top level property has a non-string type.
     *
     * @return array Strings of error messages indexed by property name.
     *
     * @psalm-return array<string, string>
     */
    public function getFirstErrorMessagesIndexedByProperty(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            if (!is_string($key)) {
                throw new InvalidArgumentException('Top level properties can only have string type.');
            }

            $errors[$key] ??= $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get an array of error objects for the property specified.
     *
     * @param string $property Property name.
     *
     * @return Error[] Array of error objects.
     *
     * @psalm-return list<Error>
     */
    public function getPropertyErrors(string $property): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $property) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * Get an array of error messages for the property specified.
     *
     * @return string[] Error messages.
     *
     * @psalm-return list<string>
     */
    public function getPropertyErrorMessages(string $property): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $property) {
                $errors[] = $error->getMessage();
            }
        }
        return $errors;
    }

    /**
     * Get an array of error messages for the path specified.
     *
     * @psalm-param list<string> $path
     * @psalm-return list<string>
     */
    public function getPropertyErrorMessagesByPath(array $path): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            if ($path === array_slice($error->getValuePath(), 0, count($path))) {
                $errors[] = $error->getMessage();
            }
        }
        return $errors;
    }

    /**
     * Get arrays of error messages for the property specified indexed by property path.
     * Each key is a dot-separated property path.
     * Each value is an array of error message strings.
     *
     * @param string $property Property name.
     * @param string $separator Property path separator. Dot is used by default.
     * @param string|null $escape Symbol that will be escaped with a backslash char (`\`) in path elements.
     * When it's null path is returned without escaping.
     *
     * @return array Arrays of error messages for the property specified indexed by property path.
     *
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getPropertyErrorMessagesIndexedByPath(
        string $property,
        string $separator = '.',
        ?string $escape = '.',
    ): array {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem !== $property) {
                continue;
            }

            $valuePath = implode($separator, array_slice($error->getValuePath($escape), 1));
            $errors[$valuePath][] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get common error messages that are not attached to any property.
     *
     * @return string[] Error messages.
     *
     * @psalm-return list<string>
     */
    public function getCommonErrorMessages(): array
    {
        return $this->getPropertyErrorMessages('');
    }

    /**
     * Add an error.
     *
     * @param string|Stringable $message The raw validation error message. See {@see Error::$message}.
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
    public function addError(string|Stringable $message, array $parameters = [], array $valuePath = []): self
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
    public function addErrorWithFormatOnly(string|Stringable $message, array $parameters = [], array $valuePath = []): self
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
    public function addErrorWithoutPostProcessing(string|Stringable $message, array $parameters = [], array $valuePath = []): self
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
