<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * MimeValidator validates that the attribute value is a valid mime type
 */
class Mime extends Rule
{
    private string $message = 'Incorrect mime type';

    private array $mimeTypes;

    public static function rule(array $mimeTypes): self
    {
        $instance = new self();
        $instance->mimeTypes = $mimeTypes;
        return $instance;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        $mimeType = $value;

        if ($value instanceof UploadedFileInterface) {
            $mimeType = $value->getClientMediaType();
        }

        if (!in_array($mimeType, $this->mimeTypes, true)) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
            ],
        );
    }
}
