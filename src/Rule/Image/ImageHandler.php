<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that a value is an image and optionally checks its dimensions.
 *
 * @see Image
 */
final class ImageHandler implements RuleHandlerInterface
{
    private ImageInfoProviderInterface $imageInfoProvider;

    public function __construct(
        ?ImageInfoProviderInterface $imageInfoProvider = null,
    ) {
        $this->imageInfoProvider = $imageInfoProvider ?? new NativeImageInfoProvider();
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Image) {
            throw new UnexpectedRuleException(Image::class, $rule);
        }

        $result = new Result();

        $imageFilePath = $this->getImageFilePath($value);
        if (empty($imageFilePath)) {
            $result->addError($rule->getNotImageMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
            ]);
            return $result;
        }

        if (!$this->shouldValidateDimensions($rule)) {
            return $result;
        }

        $info = $this->imageInfoProvider->get($imageFilePath);
        if (empty($info)) {
            $result->addError($rule->getNotImageMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
            ]);
            return $result;
        }

        $width = $info->getWidth();
        $height = $info->getHeight();

        if ($rule->getWidth() !== null && $width !== $rule->getWidth()) {
            $result->addError($rule->getNotExactWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'exactly' => $rule->getWidth(),
            ]);
        }
        if ($rule->getHeight() !== null && $height !== $rule->getHeight()) {
            $result->addError($rule->getNotExactHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'exactly' => $rule->getHeight(),
            ]);
        }
        if ($rule->getMinWidth() !== null && $width < $rule->getMinWidth()) {
            $result->addError($rule->getTooSmallWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'limit' => $rule->getMinWidth(),
            ]);
        }
        if ($rule->getMinHeight() !== null && $height < $rule->getMinHeight()) {
            $result->addError($rule->getTooSmallHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'limit' => $rule->getMinHeight(),
            ]);
        }
        if ($rule->getMaxWidth() !== null && $width > $rule->getMaxWidth()) {
            $result->addError($rule->getTooLargeWidthMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'limit' => $rule->getMaxWidth(),
            ]);
        }
        if ($rule->getMaxHeight() !== null && $height > $rule->getMaxHeight()) {
            $result->addError($rule->getTooLargeHeightMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'limit' => $rule->getMaxHeight(),
            ]);
        }

        $this->validateAspectRatio($width, $height, $rule, $context, $result);

        return $result;
    }

    private function shouldValidateDimensions(Image $rule): bool
    {
        return $rule->getWidth() !== null
            || $rule->getHeight() !== null
            || $rule->getMinHeight() !== null
            || $rule->getMinWidth() !== null
            || $rule->getMaxHeight() !== null
            || $rule->getMaxWidth() !== null
            || $rule->getAspectRatio() !== null;
    }

    private function getImageFilePath(mixed $value): ?string
    {
        $filePath = $this->getFilePath($value);
        if (empty($filePath)) {
            return null;
        }

        if (!$this->isImageFile($filePath)) {
            return null;
        }

        return $filePath;
    }

    /**
     * From PHP documentation: do not use `getimagesize()` to check that a given file is a valid image. Use
     * a purpose-built solution such as the `Fileinfo` extension instead.
     *
     * @link https://www.php.net/manual/function.getimagesize.php
     * @link https://www.php.net/manual/function.mime-content-type.php
     */
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

    private function validateAspectRatio(
        int $validatedWidth,
        int $validatedHeight,
        Image $rule,
        ValidationContext $context,
        Result $result,
    ): void {
        if ($rule->getAspectRatio() === null) {
            return;
        }

        $validatedAspectRatio = $validatedWidth / $validatedHeight;
        $expectedAspectRatio = $rule->getAspectRatio()->getWidth() / $rule->getAspectRatio()->getHeight();
        $absoluteMargin = $rule->getAspectRatio()->getMargin() / 100;

        if (
            ($validatedAspectRatio < $expectedAspectRatio - $absoluteMargin) ||
            ($validatedAspectRatio > $expectedAspectRatio + $absoluteMargin)
        ) {
            $result->addError(
                $rule->getInvalidAspectRatioMessage(),
                [
                    'attribute' => $context->getTranslatedAttribute(),
                    'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                    'aspectRatioWidth' => $rule->getAspectRatio()->getWidth(),
                    'aspectRatioHeight' => $rule->getAspectRatio()->getHeight(),
                    'aspectRatioMargin' => $rule->getAspectRatio()->getMargin(),
                ],
            );
        }
    }
}
