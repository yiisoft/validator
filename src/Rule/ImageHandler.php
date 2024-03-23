<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class ImageHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Image) {
            throw new UnexpectedRuleException(Image::class, $rule);
        }

        $result = new Result();

        $info = $this->getImageInfo($value);
        if (empty($info)) {
            $result->addError($rule->getNotImageMessage(), ['attribute' => $context->getTranslatedAttribute()]);
            return $result;
        }

        [$width, $height] = $info;

        if ($rule->getWidth() !== null && $width !== $rule->getWidth()) {
            $result->addError($rule->getNotExactlyWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'exactly' => $rule->getWidth(),
            ]);
        }
        if ($rule->getHeight() !== null && $height !== $rule->getHeight()) {
            $result->addError($rule->getNotExactlyHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'exactly' => $rule->getHeight(),
            ]);
        }
        if ($rule->getMinWidth() !== null && $width < $rule->getMinWidth()) {
            $result->addError($rule->getTooSmallWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'limit' => $rule->getMinWidth(),
            ]);
        }
        if ($rule->getMinHeight() !== null && $height < $rule->getMinHeight()) {
            $result->addError($rule->getTooSmallHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'limit' => $rule->getMinHeight(),
            ]);
        }
        if ($rule->getMaxWidth() !== null && $width > $rule->getMaxWidth()) {
            $result->addError($rule->getTooLargeWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'limit' => $rule->getMaxWidth(),
            ]);
        }
        if ($rule->getMaxHeight() !== null && $height > $rule->getMaxHeight()) {
            $result->addError($rule->getTooLargeHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'limit' => $rule->getMaxHeight(),
            ]);
        }

        return $result;
    }

    /**
     * @psalm-return array{0:int,1:int}&array
     */
    private function getImageInfo(mixed $value): ?array
    {
        $filePath = $this->getFilePath($value);
        if (empty($filePath)) {
            return null;
        }

        if (!$this->isImageFile($filePath)) {
            return null;
        }

        $info = getimagesize($filePath);
        return is_array($info) ? $info : null;
    }

    private function isImageFile(string $filePath): bool
    {
        $mimeType = @mime_content_type($filePath);
        return $mimeType !== false && str_starts_with($mimeType, 'image/');
    }

    private function getFilePath(mixed $value): ?string
    {
        if ($value instanceof UploadedFileInterface) {
            $value = $value->getError() === UPLOAD_ERR_OK ? $value->getStream()->getMetadata('uri') : null;
        }
        return is_string($value) ? $value : null;
    }
}
