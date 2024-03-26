<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Image;

use GuzzleHttp\Psr7\UploadedFile;
use Yiisoft\Validator\Rule\Image\Image;
use Yiisoft\Validator\Rule\Image\ImageHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class ImageTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Image();
        $this->assertSame('image', $rule->getName());
    }

    public function dataValidationPassed(): array
    {
        return [
            'png' => [__DIR__ . '/16x18.png', new Image()],
            'jpg' => [__DIR__ . '/16x18.jpg', new Image()],
            'heic' => [__DIR__ . '/797x808.HEIC', new Image()],
            'uploaded-file' => [new UploadedFile(__DIR__ . '/16x18.jpg', 0, UPLOAD_ERR_OK), new Image()],
            'exactly' => [__DIR__ . '/16x18.jpg', new Image(width: 16, height: 18)],
            'min-width' => [__DIR__ . '/16x18.jpg', new Image(minWidth: 12)],
            'min-width-boundary' => [__DIR__ . '/16x18.jpg', new Image(minWidth: 16)],
            'min-height' => [__DIR__ . '/16x18.jpg', new Image(minHeight: 17)],
            'min-height-boundary' => [__DIR__ . '/16x18.jpg', new Image(minHeight: 18)],
            'max-width' => [__DIR__ . '/16x18.jpg', new Image(maxWidth: 17)],
            'max-width-boundary' => [__DIR__ . '/16x18.jpg', new Image(maxWidth: 16)],
            'max-height' => [__DIR__ . '/16x18.jpg', new Image(maxHeight: 19)],
            'max-height-boundary' => [__DIR__ . '/16x18.jpg', new Image(maxHeight: 18)],
        ];
    }

    public function dataValidationFailed(): array
    {
        $notImageResult = ['' => ['The value must be an image.']];

        return [
            'heic-with-width' => [__DIR__ . '/797x808.HEIC', new Image(width: 10), $notImageResult],
            'heic-with-height' => [__DIR__ . '/797x808.HEIC', new Image(height: 10), $notImageResult],
            'heic-with-min-width' => [__DIR__ . '/797x808.HEIC', new Image(minWidth: 10), $notImageResult],
            'heic-with-max-height' => [__DIR__ . '/797x808.HEIC', new Image(minHeight: 10), $notImageResult],
            'heic-with-max-width' => [__DIR__ . '/797x808.HEIC', new Image(maxWidth: 10), $notImageResult],
            'heic-with-min-height' => [__DIR__ . '/797x808.HEIC', new Image(maxHeight: 10), $notImageResult],
            'heic-with-size-and-custom-message' => [
                ['a' => __DIR__ . '/797x808.HEIC'],
                ['a' => new Image(minWidth: 10, notImageMessage: 'The value of "{attribute}" must be an image.')],
                ['a' => ['The value of "a" must be an image.']],
            ],
            'empty-string' => ['', new Image(), $notImageResult],
            'not-file-path' => ['test', new Image(), $notImageResult],
            'not-image' => [__DIR__ . '/ImageTest.php', new Image(), $notImageResult],
            'not-image-with-custom-message' => [
                ['a' => __DIR__ . '/ImageTest.php'],
                ['a' => new Image(notImageMessage: 'The value of "{attribute}" must be an image.')],
                ['a' => ['The value of "a" must be an image.']],
            ],
            'not-uploaded-file' => [
                new UploadedFile(__DIR__ . '/16x18.jpg', 0, UPLOAD_ERR_NO_FILE),
                new Image(),
                $notImageResult,
            ],
            'not-exactly' => [
                __DIR__ . '/16x18.jpg',
                new Image(width: 24, height: 32),
                [
                    '' => [
                        'The width of image "" must be exactly 24 pixels.',
                        'The height of image "" must be exactly 32 pixels.',
                    ],
                ],
            ],
            'too-small-width' => [
                __DIR__ . '/16x18.jpg',
                new Image(minWidth: 17),
                ['' => ['The width of image "" cannot be smaller than 17 pixels.']],
            ],
            'too-small-height' => [
                __DIR__ . '/16x18.jpg',
                new Image(minHeight: 19),
                ['' => ['The height of image "" cannot be smaller than 19 pixels.']],
            ],
            'too-large-width' => [
                __DIR__ . '/16x18.jpg',
                new Image(maxWidth: 15),
                ['' => ['The width of image "" cannot be larger than 15 pixels.']],
            ],
            'too-large-height' => [
                __DIR__ . '/16x18.jpg',
                new Image(maxHeight: 17),
                ['' => ['The height of image "" cannot be larger than 17 pixels.']],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Image::class, ImageHandler::class];
    }

    public function dataOptions(): array
    {
        return [
            [
                new Image(),
                [
                    'notExactWidthMessage' => [
                        'template' => 'The width of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'exactly' => null,
                        ],
                    ],
                    'notExactHeightMessage' => [
                        'template' => 'The height of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'exactly' => null,
                        ],
                    ],
                    'tooSmallWidthMessage' => [
                        'template' => 'The width of image "{attribute}" cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => null,
                        ],
                    ],
                    'tooSmallHeightMessage' => [
                        'template' => 'The height of image "{attribute}" cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => null,
                        ],
                    ],
                    'tooLargeWidthMessage' => [
                        'template' => 'The width of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => null,
                        ],
                    ],
                    'tooLargeHeightMessage' => [
                        'template' => 'The height of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => null,
                        ],
                    ],
                    'notImageMessage' => [
                        'template' => 'The value must be an image.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Image(), new Image(skipOnError: true));
    }

    public function testWhen(): void
    {
        $this->testWhenInternal(
            new Image(),
            new Image(
                when: static fn(mixed $value): bool => $value !== null
            )
        );
    }
}
