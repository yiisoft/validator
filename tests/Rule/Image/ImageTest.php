<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Image;

use GuzzleHttp\Psr7\UploadedFile;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Image\Image;
use Yiisoft\Validator\Rule\Image\ImageAspectRatio;
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
            'width and min width' => [
                ['width' => 800, 'minWidth' => 800],
                'Exact width and min / max width can\'t be specified together.',
            ],
            'width and max width' => [
                ['width' => 800, 'maxWidth' => 800],
                'Exact width and min / max width can\'t be specified together.',
            ],
            'heifht and min height' => [
                ['height' => 600, 'minHeight' => 600],
                'Exact width and min / max height can\'t be specified together.',
            ],
            'heifht and max height' => [
                ['height' => 600, 'maxHeight' => 600],
                'Exact width and min / max height can\'t be specified together.',
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
            'default margin' => [
                new ImageInfo(800, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3)),
            ],
            'boundary width, min' => [
                new ImageInfo(794, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'boundary width, max' => [
                new ImageInfo(806, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'boundary height, min' => [
                new ImageInfo(800, 596),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'boundary height, max' => [
                new ImageInfo(800, 604),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'width within margin, smaller value' => [
                new ImageInfo(795, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'width within margin, bigger value' => [
                new ImageInfo(805, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'height within margin, smaller value' => [
                new ImageInfo(800, 597),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
            ],
            'height within margin, bigger value' => [
                new ImageInfo(800, 603),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
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
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 0%.']],
            ],
            'default aspect ratio margin, bigger height' => [
                new ImageInfo(800, 601),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 0%.']],
            ],
            'width, off by 1, smaller value' => [
                new ImageInfo(593, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'width, off by 1, bigger value' => [
                new ImageInfo(807, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'height, off by 1, smaller value' => [
                new ImageInfo(800, 593),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'height, off by 1, bigger value' => [
                new ImageInfo(800, 607),
                new Image(aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1)),
                ['' => ['The aspect ratio of image "" must be 4:3 with margin 1%.']],
            ],
            'absolute margin calculation mutant, / 100 => / 99' => [
                new ImageInfo(801, 600),
                new Image(aspectRatio: new ImageAspectRatio(width: 1, height: 3, margin: 100)),
                ['' => ['The aspect ratio of image "" must be 1:3 with margin 100%.']],
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
                    'aspectRatioMargin' => null,
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
                            'aspectRatioMargin' => null,
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
                    'aspectRatioMargin' => null,
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
                            'aspectRatioMargin' => null,
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
                    aspectRatio: new ImageAspectRatio(width: 4, height: 3, margin: 1),
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
