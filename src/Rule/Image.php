<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * @see ImageHandler
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Image implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private ?int $width = null,
        private ?int $height = null,
        private ?int $minWidth = null,
        private ?int $minHeight = null,
        private ?int $maxWidth = null,
        private ?int $maxHeight = null,
        private string $notImageMessage = 'The value must be an image.',
        private string $notExactlyWidthMessage = 'The width of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
        private string $notExactlyHeightMessage = 'The height of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
        private string $tooSmallWidthMessage = 'The width of image "{attribute}" cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooSmallHeightMessage = 'The height of image "{attribute}" cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooLargeWidthMessage = 'The width of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooLargeHeightMessage = 'The height of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getMinWidth(): ?int
    {
        return $this->minWidth;
    }

    public function getMinHeight(): ?int
    {
        return $this->minHeight;
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }

    public function getNotImageMessage(): string
    {
        return $this->notImageMessage;
    }

    public function getNotExactlyWidthMessage(): string
    {
        return $this->notExactlyWidthMessage;
    }

    public function getNotExactlyHeightMessage(): string
    {
        return $this->notExactlyHeightMessage;
    }

    public function getTooSmallWidthMessage(): string
    {
        return $this->tooSmallWidthMessage;
    }

    public function getTooSmallHeightMessage(): string
    {
        return $this->tooSmallHeightMessage;
    }

    public function getTooLargeWidthMessage(): string
    {
        return $this->tooLargeWidthMessage;
    }

    public function getTooLargeHeightMessage(): string
    {
        return $this->tooLargeHeightMessage;
    }

    public function getName(): string
    {
        return 'image';
    }

    public function getHandler(): string
    {
        return ImageHandler::class;
    }

    public function getOptions(): array
    {
        return [
            'notExactlyWidthMessage' => [
                'template' => $this->notExactlyWidthMessage,
                'parameters' => [
                    'exactly' => $this->width,
                ],
            ],
            'notExactlyHeightMessage' => [
                'template' => $this->notExactlyHeightMessage,
                'parameters' => [
                    'exactly' => $this->height,
                ],
            ],
            'tooSmallWidthMessage' => [
                'template' => $this->tooSmallWidthMessage,
                'parameters' => [
                    'limit' => $this->minWidth,
                ],
            ],
            'tooSmallHeightMessage' => [
                'template' => $this->tooSmallHeightMessage,
                'parameters' => [
                    'limit' => $this->minHeight,
                ],
            ],
            'tooLargeWidthMessage' => [
                'template' => $this->tooLargeWidthMessage,
                'parameters' => [
                    'limit' => $this->maxWidth,
                ],
            ],
            'tooLargeHeightMessage' => [
                'template' => $this->tooLargeHeightMessage,
                'parameters' => [
                    'limit' => $this->maxHeight,
                ],
            ],
            'notImageMessage' => [
                'template' => $this->notImageMessage,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }
}
