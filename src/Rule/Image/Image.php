<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

use Attribute;
use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that a value is an image and optionally check its dimensions.
 *
 * @see ImageHandler
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Image implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param int|null $width Expected exact width of validated image file.
     * @param int|null $height Expected exact height of validated image file.
     * @param int|null $minWidth Expected minimum width of validated image file.
     * @param int|null $minHeight Expected minimum height of validated image file.
     * @param int|null $maxWidth Expected maximum width of validated image file.
     * @param int|null $maxHeight Expected maximum height of validated image file.
     * @param ImageAspectRatio|null $aspectRatio Expected aspect ratio of validated image file.
     * @param string $notImageMessage A message used when the validated value is not valid image file.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     *
     * @param string $notExactWidthMessage A message used when the width of validated image file doesn't exactly equal
     * to {@see $width}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{exactly}`: expected exact width of validated image file.
     *
     * @param string $notExactHeightMessage A message used when the height of validated image file doesn't exactly equal
     * to {@see $height}.
     *
     *  You may use the following placeholders in the message:
     *
     *  - `{attribute}`: the translated label of the attribute being validated.
     *  - `{exactly}`: expected exact height of validated image file.
     *
     * @param string $tooSmallWidthMessage A message used when the width of validated image file is less than
     * {@see $minWidth}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{limit}`: expected minimum width of validated image file.
     *
     * @param string $tooSmallHeightMessage A message used when the height of validated image file is less than
     * {@see $minHeight}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{limit}`: expected minimum height of validated image file.
     *
     * @param string $tooLargeWidthMessage A message used when the width of validated image file is more than
     *  {@see $maxWidth}.
     *
     *  You may use the following placeholders in the message:
     *
     *  - `{attribute}`: the translated label of the attribute being validated.
     *  - `{limit}`: expected maximum width of validated image file.
     *
     * @param string $tooLargeHeightMessage A message used when the height of validated image file is more than
     * {@see $maxHeight}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{limit}`: expected maximum height of validated image file.
     *
     * @param string $invalidAspectRatioMessage A message used when aspect ratio of validated image file is different
     * than {@see ImageAspectRatio::$width}:{@see ImageAspectRatio::$height} with correction based on
     * {@see ImageAspectRatio::$margin}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{aspectRatioWidth}`: expected width part for aspect ratio. For example, for `4:3` aspect ratio, it will be
     * `4`.
     * - `{aspectRatioHeight}`: expected height part for aspect ratio. For example, for `4:3` aspect ratio, it will be
     *  `3`.
     * - `{aspectRatioMargin}`: expected margin for aspect ratio in percents.
     *
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
        private ?ImageAspectRatio $aspectRatio = null,
        private string $notImageMessage = '{Attribute} must be an image.',
        private string $notExactWidthMessage = 'The width must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
        private string $notExactHeightMessage = 'The height must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
        private string $tooSmallWidthMessage = 'The width cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooSmallHeightMessage = 'The height cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooLargeWidthMessage = 'The width cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $tooLargeHeightMessage = 'The height cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
        private string $invalidAspectRatioMessage = 'The aspect ratio must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if ($this->width !== null && ($this->minWidth !== null || $this->maxWidth !== null)) {
            throw new InvalidArgumentException('Exact width and min / max width can\'t be specified together.');
        }

        if ($this->height !== null && ($this->minHeight !== null || $this->maxHeight !== null)) {
            throw new InvalidArgumentException('Exact width and min / max height can\'t be specified together.');
        }
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

    public function getAspectRatio(): ?ImageAspectRatio
    {
        return $this->aspectRatio;
    }

    public function getNotImageMessage(): string
    {
        return $this->notImageMessage;
    }

    public function getNotExactWidthMessage(): string
    {
        return $this->notExactWidthMessage;
    }

    public function getNotExactHeightMessage(): string
    {
        return $this->notExactHeightMessage;
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

    public function getInvalidAspectRatioMessage(): string
    {
        return $this->invalidAspectRatioMessage;
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
            'width' => $this->width,
            'height' => $this->height,
            'minWidth' => $this->minWidth,
            'minHeight' => $this->minHeight,
            'maxWidth' => $this->maxWidth,
            'maxHeight' => $this->maxHeight,
            'aspectRatioWidth' => $this->getAspectRatio()?->getWidth(),
            'aspectRatioHeight' => $this->getAspectRatio()?->getHeight(),
            'aspectRatioMargin' => $this->getAspectRatio()?->getMargin(),
            'notExactWidthMessage' => [
                'template' => $this->notExactWidthMessage,
                'parameters' => [
                    'exactly' => $this->width,
                ],
            ],
            'notExactHeightMessage' => [
                'template' => $this->notExactHeightMessage,
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
            'invalidAspectRatioMessage' => [
                'template' => $this->invalidAspectRatioMessage,
                'parameters' => [
                    'aspectRatioWidth' => $this->getAspectRatio()?->getWidth(),
                    'aspectRatioHeight' => $this->getAspectRatio()?->getHeight(),
                    'aspectRatioMargin' => $this->getAspectRatio()?->getMargin(),
                ],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }
}
