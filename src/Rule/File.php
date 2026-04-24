<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

use function array_map;
use function array_unique;
use function is_string;
use function preg_split;
use function strtolower;
use function trim;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Defines validation options to check that a value is a valid file and optionally validate its extension, MIME type
 * and size.
 *
 * Supported values are:
 *
 * - string file paths;
 * - {@see \SplFileInfo} instances;
 * - {@see UploadedFileInterface} instances.
 *
 * Use {@see Each} when validating multiple files.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 *
 * @see FileHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class File implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var list<string>|null
     */
    private ?array $extensions;

    /**
     * @var list<string>|null
     */
    private ?array $mimeTypes;

    /**
     * @param array|string|null $extensions Allowed file extensions without a leading dot. Values are case-insensitive
     * and may be provided either as an array or as a comma / space separated string. Files without extension will not
     * pass validation if it is configured.
     * @param array|string|null $mimeTypes Allowed MIME types. Values are case-insensitive and may be provided either
     * as an array or as a comma / space separated string. Wildcards like `image/*` are supported. For in-memory
     * stream uploads without a real file path, MIME validation falls back to client-provided metadata. If
     * `mime_content_type()` is unavailable, MIME checks for filesystem-backed files will fail validation.
     * @param int|null $size Expected exact size of the validated file in bytes. Validation fails if size cannot be
     * determined.
     * @param int|null $minSize Expected minimum size of the validated file in bytes. Validation fails if size cannot
     * be determined.
     * @param int|null $maxSize Expected maximum size of the validated file in bytes. Validation fails if size cannot
     * be determined.
     * @param string $message A message used when the validated value is not a valid file.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * @param string $uploadFailedMessage A message used when uploaded file contains an upload error.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{error}`: the upload error code.
     * @param string $uploadRequiredMessage A message used when no file was provided.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * @param string $wrongExtensionMessage A message used when the file extension is not allowed.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{extensions}`: the list of allowed extensions.
     * @param string $wrongMimeTypeMessage A message used when the file MIME type is not allowed.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{mimeTypes}`: the list of allowed MIME types.
     * @param string $notExactSizeMessage A message used when the file size doesn't exactly equal {@see $size}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{exactly}`: expected exact size in bytes.
     * @param string $tooSmallMessage A message used when the file size is less than {@see $minSize}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{limit}`: expected minimum size in bytes.
     * @param string $tooBigMessage A message used when the file size is greater than {@see $maxSize}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * - `{limit}`: expected maximum size in bytes.
     * @param string $unableToDetermineSizeMessage A message used when file size constraints are configured, but the
     * file size can't be determined.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{file}`: the validated file name when it is available.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param list<string>|string|null $extensions
     * @psalm-param list<string>|string|null $mimeTypes
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        array|string|null $extensions = null,
        array|string|null $mimeTypes = null,
        private ?int $size = null,
        private ?int $minSize = null,
        private ?int $maxSize = null,
        private string $message = '{Property} must be a file.',
        private string $uploadFailedMessage = 'Failed to upload {property}. Error code: {error, number}.',
        private string $uploadRequiredMessage = 'Please upload a file.',
        private string $wrongExtensionMessage = 'Only files with these extensions are allowed: {extensions}.',
        private string $wrongMimeTypeMessage = 'Only files with these MIME types are allowed: {mimeTypes}.',
        private string $notExactSizeMessage = 'The size of {property} must be exactly {exactly, number} {exactly, plural, one{byte} other{bytes}}.',
        private string $tooSmallMessage = 'The size of {property} cannot be smaller than {limit, number} {limit, plural, one{byte} other{bytes}}.',
        private string $tooBigMessage = 'The size of {property} cannot be larger than {limit, number} {limit, plural, one{byte} other{bytes}}.',
        private string $unableToDetermineSizeMessage = 'The size of {property} cannot be determined.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        if ($this->size !== null && ($this->minSize !== null || $this->maxSize !== null)) {
            throw new InvalidArgumentException('Exact size and min / max size can\'t be specified together.');
        }

        foreach (['size' => $this->size, 'minSize' => $this->minSize, 'maxSize' => $this->maxSize] as $name => $value) {
            if ($value !== null && $value < 0) {
                throw new InvalidArgumentException(ucfirst($name) . ' must be greater than or equal to 0.');
            }
        }

        $this->extensions = $this->normalizeList($extensions);
        $this->mimeTypes = $this->normalizeList($mimeTypes);
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return 'file';
    }

    /**
     * Get allowed file extensions.
     *
     * @return list<string>|null
     *
     * @see $extensions
     */
    public function getExtensions(): ?array
    {
        return $this->extensions;
    }

    /**
     * Get allowed file MIME types.
     *
     * @return list<string>|null
     *
     * @see $mimeTypes
     */
    public function getMimeTypes(): ?array
    {
        return $this->mimeTypes;
    }

    /**
     * Get expected exact file size in bytes.
     *
     * @return int|null Expected exact file size in bytes.
     *
     * @see $size
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Get expected minimum file size in bytes.
     *
     * @return int|null Expected minimum file size in bytes.
     *
     * @see $minSize
     */
    public function getMinSize(): ?int
    {
        return $this->minSize;
    }

    /**
     * Get expected maximum file size in bytes.
     *
     * @return int|null Expected maximum file size in bytes.
     *
     * @see $maxSize
     */
    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    /**
     * Get error message used when the validated value is not a file.
     *
     * @return string Error message.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get error message used when an uploaded file contains an upload error.
     *
     * @return string Error message.
     *
     * @see $uploadFailedMessage
     */
    public function getUploadFailedMessage(): string
    {
        return $this->uploadFailedMessage;
    }

    /**
     * Get error message used when no file was provided.
     *
     * @return string Error message.
     *
     * @see $uploadRequiredMessage
     */
    public function getUploadRequiredMessage(): string
    {
        return $this->uploadRequiredMessage;
    }

    /**
     * Get error message used when the file extension is not allowed.
     *
     * @return string Error message.
     *
     * @see $wrongExtensionMessage
     */
    public function getWrongExtensionMessage(): string
    {
        return $this->wrongExtensionMessage;
    }

    /**
     * Get error message used when the file MIME type is not allowed.
     *
     * @return string Error message.
     *
     * @see $wrongMimeTypeMessage
     */
    public function getWrongMimeTypeMessage(): string
    {
        return $this->wrongMimeTypeMessage;
    }

    /**
     * Get error message used when the file size doesn't exactly equal {@see $size}.
     *
     * @return string Error message.
     *
     * @see $notExactSizeMessage
     */
    public function getNotExactSizeMessage(): string
    {
        return $this->notExactSizeMessage;
    }

    /**
     * Get error message used when the file size is less than {@see $minSize}.
     *
     * @return string Error message.
     *
     * @see $tooSmallMessage
     */
    public function getTooSmallMessage(): string
    {
        return $this->tooSmallMessage;
    }

    /**
     * Get error message used when the file size is greater than {@see $maxSize}.
     *
     * @return string Error message.
     *
     * @see $tooBigMessage
     */
    public function getTooBigMessage(): string
    {
        return $this->tooBigMessage;
    }

    /**
     * Get error message used when the file size cannot be determined for configured size constraints.
     *
     * @return string Error message.
     *
     * @see $unableToDetermineSizeMessage
     */
    public function getUnableToDetermineSizeMessage(): string
    {
        return $this->unableToDetermineSizeMessage;
    }

    public function getHandler(): string
    {
        return FileHandler::class;
    }

    public function getOptions(): array
    {
        return [
            'extensions' => $this->extensions,
            'mimeTypes' => $this->mimeTypes,
            'size' => $this->size,
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'uploadFailedMessage' => [
                'template' => $this->uploadFailedMessage,
                'parameters' => [],
            ],
            'uploadRequiredMessage' => [
                'template' => $this->uploadRequiredMessage,
                'parameters' => [],
            ],
            'wrongExtensionMessage' => [
                'template' => $this->wrongExtensionMessage,
                'parameters' => [],
            ],
            'wrongMimeTypeMessage' => [
                'template' => $this->wrongMimeTypeMessage,
                'parameters' => [],
            ],
            'notExactSizeMessage' => [
                'template' => $this->notExactSizeMessage,
                'parameters' => [],
            ],
            'tooSmallMessage' => [
                'template' => $this->tooSmallMessage,
                'parameters' => [],
            ],
            'tooBigMessage' => [
                'template' => $this->tooBigMessage,
                'parameters' => [],
            ],
            'unableToDetermineSizeMessage' => [
                'template' => $this->unableToDetermineSizeMessage,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    /**
     * @psalm-param list<string>|string|null $value
     *
     * @return list<string>|null
     */
    private function normalizeList(array|string|null $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $value = array_values(
            array_unique(
                array_map(
                    static fn(string $item): string => strtolower(trim($item)),
                    $value,
                ),
            ),
        );

        if ($value === []) {
            throw new InvalidArgumentException('List of allowed values cannot be empty.');
        }

        return $value;
    }
}
