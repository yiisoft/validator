<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Image;

use GuzzleHttp\Psr7\UploadedFile;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Image\Image;
use Yiisoft\Validator\Rule\Image\ImageHandler;
use Yiisoft\Validator\Rule\Image\ImageInfo;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\RuleHandlerResolver\ImageHandlerContainer;
use Yiisoft\Validator\Validator;

final class ImageTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function dataConfigurationError(): array
    {
        return [
            'aspect ratio, height is missing' => [
                ['aspectRatioWidth' => 800],
                'Aspect ratio width and height must be specified together.',
            ],
            'aspect ratio, width is missing' => [
                ['aspectRatioHeight' => 600],
                'Aspect ratio width and height must be specified together.',
            ],
        ];
    }

    /**
     * @dataProvider dataConfigurationError
     */
    public function testConfigurationError(array $arguments, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Image(...$arguments);
    }

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

    public function dataValidationPassedAspectRatio(): array
    {
        return [
            'default margin' => [new ImageInfo(800, 600), new Image(aspectRatioWidth: 4, aspectRatioHeight: 3)],
            'boundary height, min' => [
                new ImageInfo(800, 594),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
            ],
            'height within margin, smaller value' => [
                new ImageInfo(800, 595),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
            ],
            'height within margin, bigger value' => [
                new ImageInfo(800, 605),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
            ],
            'boundary height, max' => [
                new ImageInfo(800, 606),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
            ],
        ];
    }

    /**
     * @dataProvider dataValidationPassedAspectRatio
     */
    public function testValidationPassedAspectRatio(ImageInfo $imageInfo, Image $rules): void
    {
        $result = (new Validator(new ImageHandlerContainer($imageInfo)))->validate(__DIR__ . '/16x18.jpg', $rules);
        $this->assertSame([], $result->getErrorMessagesIndexedByPath());
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

    public function dataValidationFailedAspectRatio(): array
    {
        return [
            'default aspect ratio margin , smaller height' => [
                new ImageInfo(800, 599),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 0%.']],
            ],
            'default aspect ratio margin, bigger height' => [
                new ImageInfo(800, 601),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 0%.']],
            ],
            'height, off by 1, smaller value' => [
                new ImageInfo(800, 593),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'height, off by 1, bigger value' => [
                new ImageInfo(800, 607),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 1),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'absolute margin calculation mutant, / 100 => / 99' => [
                new ImageInfo(800, 721),
                new Image(aspectRatioWidth: 4, aspectRatioHeight: 3, aspectRatioMargin: 20),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 20%.']],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailedAspectRatio
     */
    public function testValidationFailedAspectRatio(ImageInfo $imageInfo, Image $rules, array $errors): void
    {
        $result = (new Validator(new ImageHandlerContainer($imageInfo)))->validate(__DIR__ . '/16x18.jpg', $rules);
        $this->assertSame($errors, $result->getErrorMessagesIndexedByPath());
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Image::class, ImageHandler::class];
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new Image(),
                [
                    'width' => null,
                    'height' => null,
                    'minWidth' => null,
                    'minHeight' => null,
                    'maxWidth' => null,
                    'maxHeight' => null,
                    'aspectRatioWidth' => null,
                    'aspectRatioHeight' => null,
                    'aspectRatioMargin' => 0.0,
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
                    'invalidAspectRatioMessage' => [
                        'template' => 'The aspect ratio of image "{attribute}" must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.',
                        'parameters' => [
                            'aspectRatioWidth' => null,
                            'aspectRatioHeight' => null,
                            'aspectRatioMargin' => 0.0,
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'exact width and height' => [
                new Image(width: 800, height: 600),
                [
                    'width' => 800,
                    'height' => 600,
                    'minWidth' => null,
                    'minHeight' => null,
                    'maxWidth' => null,
                    'maxHeight' => null,
                    'aspectRatioWidth' => null,
                    'aspectRatioHeight' => null,
                    'aspectRatioMargin' => 0.0,
                    'notExactWidthMessage' => [
                        'template' => 'The width of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'exactly' => 800,
                        ],
                    ],
                    'notExactHeightMessage' => [
                        'template' => 'The height of image "{attribute}" must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'exactly' => 600,
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
                    'invalidAspectRatioMessage' => [
                        'template' => 'The aspect ratio of image "{attribute}" must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.',
                        'parameters' => [
                            'aspectRatioWidth' => null,
                            'aspectRatioHeight' => null,
                            'aspectRatioMargin' => 0.0,
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'min and max height with aspect ratio' => [
                new Image(
                    minWidth: 700,
                    minHeight: 550,
                    maxWidth: 900,
                    maxHeight: 750,
                    aspectRatioWidth: 4,
                    aspectRatioHeight: 3,
                    aspectRatioMargin: 1,
                ),
                [
                    'width' => null,
                    'height' => null,
                    'minWidth' => 700,
                    'minHeight' => 550,
                    'maxWidth' => 900,
                    'maxHeight' => 750,
                    'aspectRatioWidth' => 4,
                    'aspectRatioHeight' => 3,
                    'aspectRatioMargin' => 1.0,
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
                            'limit' => 700,
                        ],
                    ],
                    'tooSmallHeightMessage' => [
                        'template' => 'The height of image "{attribute}" cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => 550,
                        ],
                    ],
                    'tooLargeWidthMessage' => [
                        'template' => 'The width of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => 900,
                        ],
                    ],
                    'tooLargeHeightMessage' => [
                        'template' => 'The height of image "{attribute}" cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.',
                        'parameters' => [
                            'limit' => 750,
                        ],
                    ],
                    'notImageMessage' => [
                        'template' => 'The value must be an image.',
                        'parameters' => [],
                    ],
                    'invalidAspectRatioMessage' => [
                        'template' => 'The aspect ratio of image "{attribute}" must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.',
                        'parameters' => [
                            'aspectRatioWidth' => 4,
                            'aspectRatioHeight' => 3,
                            'aspectRatioMargin' => 1.0,
                        ],
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
