<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Psr\Http\Message\UploadedFileInterface;
use SplFileInfo;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;
use RuntimeException;

use function basename;
use function implode;
use function in_array;
use function is_file;
use function is_string;
use function pathinfo;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function function_exists;
use function is_int;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

/**
 * Validates that a value is a file and optionally checks its extension, MIME type and size.
 *
 * @see File
 *
 * @psalm-type FileData = array{
 *     status: 'missing'|'upload-error'|'invalid'|'ok',
 *     name: string,
 *     size: int|null,
 *     path: string|null,
 *     error: int|null,
 *     clientMediaType: string|null,
 * }
 */
final class FileHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof File) {
            throw new UnexpectedRuleException(File::class, $rule);
        }

        $result = new Result();
        $file = $this->getFileData($value);

        if ($file['status'] === 'missing') {
            $result->addError($rule->getUploadRequiredMessage(), $this->getParameters($context));
            return $result;
        }

        if ($file['status'] === 'upload-error') {
            $result->addError(
                $rule->getUploadFailedMessage(),
                $this->getParameters($context, $file, ['error' => $file['error']]),
            );
            return $result;
        }

        if ($file['status'] !== 'ok') {
            $result->addError($rule->getMessage(), $this->getParameters($context, $file));
            return $result;
        }

        if (!$this->isExtensionValid($file['name'], $rule->getExtensions())) {
            $result->addError(
                $rule->getWrongExtensionMessage(),
                $this->getParameters($context, $file, ['extensions' => implode(', ', $rule->getExtensions() ?? [])]),
            );
        }

        if (!$this->isMimeTypeValid($file, $rule->getMimeTypes())) {
            $result->addError(
                $rule->getWrongMimeTypeMessage(),
                $this->getParameters($context, $file, ['mimeTypes' => implode(', ', $rule->getMimeTypes() ?? [])]),
            );
        }

        $this->validateSize($file, $rule, $context, $result);

        return $result;
    }

    /**
     * @psalm-return FileData
     */
    private function getFileData(mixed $value): array
    {
        if ($value instanceof UploadedFileInterface) {
            $error = $value->getError();
            $name = $this->normalizeFileName($value->getClientFilename());

            if ($error === UPLOAD_ERR_NO_FILE) {
                return [
                    'status' => 'missing',
                    'name' => $name,
                    'size' => null,
                    'path' => null,
                    'error' => $error,
                    'clientMediaType' => null,
                ];
            }

            if ($error !== UPLOAD_ERR_OK) {
                return [
                    'status' => 'upload-error',
                    'name' => $name,
                    'size' => null,
                    'path' => null,
                    'error' => $error,
                    'clientMediaType' => null,
                ];
            }

            try {
                $path = $this->getUploadedFilePath($value);
            } catch (RuntimeException) {
                return [
                    'status' => 'invalid',
                    'name' => $name,
                    'size' => null,
                    'path' => null,
                    'error' => null,
                    'clientMediaType' => $value->getClientMediaType(),
                ];
            }

            $name = $name !== '' ? $name : $this->normalizeFileName($path);
            $size = $value->getSize();
            $isFile = $path === null || is_file($path);

            if ($size === null && $path !== null && $isFile) {
                $fileInfoSize = (new SplFileInfo($path))->getSize();
                $size = is_int($fileInfoSize) ? $fileInfoSize : null;
            }

            return [
                'status' => $isFile ? 'ok' : 'invalid',
                'name' => $name,
                'size' => $size,
                'path' => $path,
                'error' => null,
                'clientMediaType' => $value->getClientMediaType(),
            ];
        }

        if ($value instanceof SplFileInfo) {
            $isFile = $value->isFile();
            $size = null;

            if ($isFile) {
                $fileInfoSize = $value->getSize();
                $size = is_int($fileInfoSize) ? $fileInfoSize : null;
            }

            return [
                'status' => $isFile ? 'ok' : 'invalid',
                'name' => $this->normalizeFileName($value->getFilename()),
                'size' => $size,
                'path' => $isFile ? $value->getPathname() : null,
                'error' => null,
                'clientMediaType' => null,
            ];
        }

        if ($value === null || $value === '') {
            return [
                'status' => 'missing',
                'name' => '',
                'size' => null,
                'path' => null,
                'error' => null,
                'clientMediaType' => null,
            ];
        }

        if (is_string($value)) {
            $isFile = is_file($value);
            $size = null;

            if ($isFile) {
                $fileInfoSize = (new SplFileInfo($value))->getSize();
                $size = is_int($fileInfoSize) ? $fileInfoSize : null;
            }

            return [
                'status' => $isFile ? 'ok' : 'invalid',
                'name' => $this->normalizeFileName($value),
                'size' => $size,
                'path' => $isFile ? $value : null,
                'error' => null,
                'clientMediaType' => null,
            ];
        }

        return [
            'status' => 'invalid',
            'name' => '',
            'size' => null,
            'path' => null,
            'error' => null,
            'clientMediaType' => null,
        ];
    }

    /**
     * @psalm-param list<string>|null $extensions
     */
    private function isExtensionValid(string $fileName, ?array $extensions): bool
    {
        if ($extensions === null) {
            return true;
        }

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return $extension !== '' && in_array($extension, $extensions, true);
    }

    /**
     * @psalm-param FileData $file
     * @psalm-param list<string>|null $mimeTypes
     */
    private function isMimeTypeValid(array $file, ?array $mimeTypes): bool
    {
        if ($mimeTypes === null) {
            return true;
        }

        $mimeType = $this->detectMimeType($file);
        if ($mimeType === null) {
            return false;
        }
        $mimeType = strtolower($mimeType);

        foreach ($mimeTypes as $allowedMimeType) {
            if ($allowedMimeType === $mimeType) {
                return true;
            }

            if (str_ends_with($allowedMimeType, '/*') && str_starts_with($mimeType, substr($allowedMimeType, 0, -1))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @psalm-param FileData $file
     */
    private function validateSize(array $file, File $rule, ValidationContext $context, Result $result): void
    {
        $size = $file['size'];
        if ($size === null) {
            return;
        }

        if ($rule->getSize() !== null && $size !== $rule->getSize()) {
            $result->addError(
                $rule->getNotExactSizeMessage(),
                $this->getParameters($context, $file, ['exactly' => $rule->getSize()]),
            );
        }

        if ($rule->getMinSize() !== null && $size < $rule->getMinSize()) {
            $result->addError(
                $rule->getTooSmallMessage(),
                $this->getParameters($context, $file, ['limit' => $rule->getMinSize()]),
            );
        }

        if ($rule->getMaxSize() !== null && $size > $rule->getMaxSize()) {
            $result->addError(
                $rule->getTooBigMessage(),
                $this->getParameters($context, $file, ['limit' => $rule->getMaxSize()]),
            );
        }
    }

    /**
     * @psalm-param FileData $file
     */
    private function detectMimeType(array $file): ?string
    {
        if ($file['path'] !== null && is_file($file['path'])) {
            if (function_exists('mime_content_type')) {
                $mimeType = mime_content_type($file['path']);
            } else {
                return null;
            }

            return is_string($mimeType) ? $mimeType : null;
        }

        return $file['clientMediaType'];
    }

    private function getUploadedFilePath(UploadedFileInterface $value): ?string
    {
        $uri = $value->getStream()->getMetadata('uri');
        if (!is_string($uri)) {
            return null;
        }

        if ($uri === '') {
            return null;
        }

        if (str_starts_with($uri, 'php://')) {
            return null;
        }

        return $uri;
    }

    private function normalizeFileName(?string $name): string
    {
        if ($name === null || $name === '') {
            return '';
        }

        return str_contains($name, '\\') ? basename(str_replace('\\', '/', $name)) : basename($name);
    }

    /**
     * @psalm-param FileData|null $file
     * @psalm-param array<string, scalar|null> $extra
     *
     * @psalm-return array<string, scalar|null>
     */
    private function getParameters(ValidationContext $context, ?array $file = null, array $extra = []): array
    {
        return [
            'property' => $context->getTranslatedProperty(),
            'Property' => $context->getCapitalizedTranslatedProperty(),
            'file' => $file['name'] ?? '',
            ...$extra,
        ];
    }
}
