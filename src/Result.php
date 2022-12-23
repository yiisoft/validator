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
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors messages as an array of strings.
     *
     * @return string[] Array messages as strings.
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
     *
     * @return array Arrays of error messages indexed by attribute path.
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorMessagesIndexedByPath(string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath(true));
            $errors[$stringValuePath][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * Get arrays of error messages indexed by attribute name.
     *
     * @throws InvalidArgumentException If top level attribute has a non-string type.
     *
     * @return array Arrays of error messages indexed by attribute name.
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
     * Get an array of error objects for the attribute specified.
     *
     * @param string $attribute Attribute name.
     *
     * @return Error[] Array of error objects.
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
     *
     * @return array
     * @return array<string, non-empty-list<string>>
     */
    public function getAttributeErrorMessagesIndexedByPath(string $attribute, string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem !== $attribute) {
                continue;
            }

            $valuePath = implode($separator, array_slice($error->getValuePath(true), 1));
            $errors[$valuePath][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * Get common error messages that are not attached to any attribute.
     *
     * @return string[] Error messages.
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
     * @psalm-param array<string,scalar|null> $parameters
     *
     * @param array $valuePath A sequence of keys determining where a value caused the validation
     * error is located within a nested structure. See {@see Error::$valuePath}.
     * @psalm-param list<int|string> $valuePath
     *
     * @return $this Same instance of result.
     */
    public function addError(string $message, array $parameters = [], array $valuePath = []): self
    {
        $this->errors[] = new Error($message, $parameters, $valuePath);

        return $this;
    }
}
