<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use GuzzleHttp\Psr7\UploadedFile;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use SplFileInfo;
use stdClass;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\File;
use Yiisoft\Validator\Rule\FileHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

use function fopen;
use function fwrite;
use function rewind;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

final class FileTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public const JPG_FILE = __DIR__ . '/Image/16x18.jpg';
    public const PNG_FILE = __DIR__ . '/Image/16x18.png';
    public const EMPTY_JPG_FILE = __DIR__ . '/Image/not-image.jpg';
    public const TEXT_FILE = __DIR__ . '/File/notes.txt';
    public const EXTENSIONLESS_FILE = __DIR__ . '/File/README';

    public static function dataConfigurationError(): array
    {
        return [
            'size and min size' => [
                ['size' => 100, 'minSize' => 100],
                'Exact size and min / max size can\'t be specified together.',
            ],
            'size and max size' => [
                ['size' => 100, 'maxSize' => 100],
                'Exact size and min / max size can\'t be specified together.',
            ],
            'negative size' => [
                ['size' => -1],
                'Size must be greater than or equal to 0.',
            ],
            'negative min size' => [
                ['minSize' => -1],
                'MinSize must be greater than or equal to 0.',
            ],
            'negative max size' => [
                ['maxSize' => -1],
                'MaxSize must be greater than or equal to 0.',
            ],
            'empty extensions list' => [
                ['extensions' => ' , '],
                'List of allowed values cannot be empty.',
            ],
            'empty mime types list' => [
                ['mimeTypes' => []],
                'List of allowed values cannot be empty.',
            ],
        ];
    }

    #[DataProvider('dataConfigurationError')]
    public function testConfigurationError(array $arguments, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new File(...$arguments);
    }

    public function testGetName(): void
    {
        $rule = new File();
        $this->assertSame('file', $rule->getName());
    }

    public function testAllowedValuesAreNormalizedFromArrayInput(): void
    {
        $rule = new File(
            extensions: [' JPG ', 'jpg', 'Png '],
            mimeTypes: [' TEXT/PLAIN ', 'text/plain', 'IMAGE/JPEG '],
        );

        $this->assertSame(['jpg', 'png'], $rule->getExtensions());
        $this->assertSame(['text/plain', 'image/jpeg'], $rule->getMimeTypes());
    }

    public static function dataOptions(): array
    {
        return [
            [
                new File(),
                [
                    'extensions' => null,
                    'mimeTypes' => null,
                    'size' => null,
                    'minSize' => null,
                    'maxSize' => null,
                    'message' => [
                        'template' => '{Property} must be a file.',
                        'parameters' => [],
                    ],
                    'uploadFailedMessage' => [
                        'template' => 'Failed to upload {property}. Error code: {error, number}.',
                        'parameters' => [],
                    ],
                    'uploadRequiredMessage' => [
                        'template' => 'Please upload a file.',
                        'parameters' => [],
                    ],
                    'wrongExtensionMessage' => [
                        'template' => 'Only files with these extensions are allowed: {extensions}.',
                        'parameters' => [],
                    ],
                    'wrongMimeTypeMessage' => [
                        'template' => 'Only files with these MIME types are allowed: {mimeTypes}.',
                        'parameters' => [],
                    ],
                    'notExactSizeMessage' => [
                        'template' => 'The size of {property} must be exactly {exactly, number} {exactly, plural, one{byte} other{bytes}}.',
                        'parameters' => [],
                    ],
                    'tooSmallMessage' => [
                        'template' => 'The size of {property} cannot be smaller than {limit, number} {limit, plural, one{byte} other{bytes}}.',
                        'parameters' => [],
                    ],
                    'tooBigMessage' => [
                        'template' => 'The size of {property} cannot be larger than {limit, number} {limit, plural, one{byte} other{bytes}}.',
                        'parameters' => [],
                    ],
                    'unableToDetermineSizeMessage' => [
                        'template' => 'The size of {property} cannot be determined.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new File(
                    extensions: '  JPG, jpg  , png ',
                    mimeTypes: [' IMAGE/JPEG ', 'text/plain', 'TEXT/PLAIN'],
                    size: 921,
                    message: 'Custom file message.',
                    uploadFailedMessage: 'Custom upload failed.',
                    uploadRequiredMessage: 'Custom upload required.',
                    wrongExtensionMessage: 'Custom extension.',
                    wrongMimeTypeMessage: 'Custom mime.',
                    notExactSizeMessage: 'Custom exact size.',
                    tooSmallMessage: 'Custom too small.',
                    tooBigMessage: 'Custom too big.',
                    unableToDetermineSizeMessage: 'Custom unknown size.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'extensions' => ['jpg', 'png'],
                    'mimeTypes' => ['image/jpeg', 'text/plain'],
                    'size' => 921,
                    'minSize' => null,
                    'maxSize' => null,
                    'message' => [
                        'template' => 'Custom file message.',
                        'parameters' => [],
                    ],
                    'uploadFailedMessage' => [
                        'template' => 'Custom upload failed.',
                        'parameters' => [],
                    ],
                    'uploadRequiredMessage' => [
                        'template' => 'Custom upload required.',
                        'parameters' => [],
                    ],
                    'wrongExtensionMessage' => [
                        'template' => 'Custom extension.',
                        'parameters' => [],
                    ],
                    'wrongMimeTypeMessage' => [
                        'template' => 'Custom mime.',
                        'parameters' => [],
                    ],
                    'notExactSizeMessage' => [
                        'template' => 'Custom exact size.',
                        'parameters' => [],
                    ],
                    'tooSmallMessage' => [
                        'template' => 'Custom too small.',
                        'parameters' => [],
                    ],
                    'tooBigMessage' => [
                        'template' => 'Custom too big.',
                        'parameters' => [],
                    ],
                    'unableToDetermineSizeMessage' => [
                        'template' => 'Custom unknown size.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            'exact zero size' => [self::EMPTY_JPG_FILE, new File(size: 0)],
            'file path' => [self::JPG_FILE, new File()],
            'spl file info' => [new SplFileInfo(self::JPG_FILE), new File()],
            'spl file info with constraints' => [
                new SplFileInfo(self::TEXT_FILE),
                new File(mimeTypes: ['text/plain'], size: 22),
            ],
            'min size zero' => [self::EMPTY_JPG_FILE, new File(minSize: 0)],
            'uploaded file from path' => [
                new UploadedFile(self::JPG_FILE, 921, UPLOAD_ERR_OK, 'avatar.JPG', 'image/jpeg'),
                new File(extensions: ['jpg'], mimeTypes: ['image/jpeg'], size: 921),
            ],
            'uploaded file from path without client metadata' => [
                new UploadedFile(self::JPG_FILE, 999, UPLOAD_ERR_OK),
                new File(extensions: ['jpg'], mimeTypes: ['image/jpeg'], size: 999),
            ],
            'uploaded file from stream with client metadata' => [
                self::createStreamUpload('resume.txt', 'text/plain'),
                new File(extensions: 'txt', mimeTypes: 'text/plain', size: 22),
            ],
            'uploaded file from stream with uppercase client metadata' => [
                self::createStreamUpload('resume.txt', 'TEXT/PLAIN'),
                new File(extensions: 'txt', mimeTypes: 'text/plain', size: 22),
            ],
            'uploaded file from php stream uri without filename' => [
                self::createStreamUpload(null, 'text/plain', 22),
                new File(mimeTypes: 'text/plain', size: 22),
            ],
            'uploaded file from stream with unknown size' => [
                self::createStreamUpload('resume.txt', 'text/plain', null),
                new File(extensions: 'txt', mimeTypes: 'text/plain'),
            ],
            'mime wildcard' => [self::PNG_FILE, new File(mimeTypes: ['image/*'])],
            'min size boundary' => [self::TEXT_FILE, new File(minSize: 22)],
            'max size boundary' => [self::TEXT_FILE, new File(maxSize: 22)],
            'multiple files via each rule' => [
                [self::JPG_FILE, new SplFileInfo(self::TEXT_FILE)],
                new Each(new File()),
            ],
            'null with skipOnEmpty' => [null, new File(skipOnEmpty: true)],
            'null with when returning false' => [
                null,
                new File(when: static fn(mixed $value): bool => $value !== null),
            ],
            'object providing rules and valid data' => [
                new class {
                    #[File(extensions: 'txt', mimeTypes: 'text/plain', size: 22)]
                    private string $file = FileTest::TEXT_FILE;
                },
                null,
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        return [
            'missing string value' => [null, new File(), ['' => ['Please upload a file.']]],
            'empty string value' => ['', new File(), ['' => ['Please upload a file.']]],
            'uploaded file missing' => [
                new UploadedFile(self::JPG_FILE, 921, UPLOAD_ERR_NO_FILE),
                new File(),
                ['' => ['Please upload a file.']],
            ],
            'uploaded file error' => [
                new UploadedFile(self::JPG_FILE, 921, UPLOAD_ERR_CANT_WRITE, 'avatar.jpg'),
                new File(),
                ['' => ['Failed to upload value. Error code: 7.']],
            ],
            'uploaded file with missing temp path and unknown size' => [
                new UploadedFile('/definitely/missing/upload.tmp', null, UPLOAD_ERR_OK, 'avatar.jpg'),
                new File(),
                ['' => ['Value must be a file.']],
            ],
            'non file path' => ['missing.txt', new File(), ['' => ['Value must be a file.']]],
            'invalid value type' => [new stdClass(), new File(), ['' => ['Value must be a file.']]],
            'invalid value type with constraints' => [
                new stdClass(),
                new File(extensions: ['jpg'], mimeTypes: ['image/jpeg'], size: 1),
                ['' => ['Value must be a file.']],
            ],
            'spl file info directory' => [
                new SplFileInfo(__DIR__ . '/File'),
                new File(),
                ['' => ['Value must be a file.']],
            ],
            'wrong extension' => [
                self::TEXT_FILE,
                new File(extensions: ['jpg']),
                ['' => ['Only files with these extensions are allowed: jpg.']],
            ],
            'extensionless file with extensions constraint' => [
                self::EXTENSIONLESS_FILE,
                new File(extensions: ['txt']),
                ['' => ['Only files with these extensions are allowed: txt.']],
            ],
            'wrong mime type' => [
                self::TEXT_FILE,
                new File(mimeTypes: ['image/jpeg']),
                ['' => ['Only files with these MIME types are allowed: image/jpeg.']],
            ],
            'spl file info exact size mismatch' => [
                new SplFileInfo(self::TEXT_FILE),
                new File(size: 21),
                ['' => ['The size of value must be exactly 21 bytes.']],
            ],
            'exact size mismatch' => [
                self::JPG_FILE,
                new File(size: 920),
                ['' => ['The size of value must be exactly 920 bytes.']],
            ],
            'too small' => [
                self::EMPTY_JPG_FILE,
                new File(minSize: 1),
                ['' => ['The size of value cannot be smaller than 1 byte.']],
            ],
            'too big' => [
                self::JPG_FILE,
                new File(maxSize: 920),
                ['' => ['The size of value cannot be larger than 920 bytes.']],
            ],
            'stream upload unknown exact size' => [
                self::createStreamUpload('resume.txt', 'text/plain', null),
                new File(extensions: 'txt', mimeTypes: 'text/plain', size: 22),
                ['' => ['The size of value cannot be determined.']],
            ],
            'stream upload unknown minimum size' => [
                self::createStreamUpload('resume.txt', 'text/plain', null),
                new File(extensions: 'txt', mimeTypes: 'text/plain', minSize: 1),
                ['' => ['The size of value cannot be determined.']],
            ],
            'stream upload unknown maximum size' => [
                self::createStreamUpload('resume.txt', 'text/plain', null),
                new File(extensions: 'txt', mimeTypes: 'text/plain', maxSize: 100),
                ['' => ['The size of value cannot be determined.']],
            ],
            'stream upload wrong extension' => [
                self::createStreamUpload('resume.txt', 'text/plain'),
                new File(extensions: ['jpg']),
                ['' => ['Only files with these extensions are allowed: jpg.']],
            ],
            'stream upload missing client media type' => [
                self::createStreamUpload('resume.txt', null),
                new File(mimeTypes: ['text/plain']),
                ['' => ['Only files with these MIME types are allowed: text/plain.']],
            ],
            'stream upload missing client media type with wildcard rule' => [
                self::createStreamUpload('resume.txt', null),
                new File(mimeTypes: ['image/*']),
                ['' => ['Only files with these MIME types are allowed: image/*.']],
            ],
            'stream upload wildcard mismatch' => [
                self::createStreamUpload('resume.txt', 'text/plain'),
                new File(mimeTypes: ['image/*']),
                ['' => ['Only files with these MIME types are allowed: image/*.']],
            ],
            'custom messages with parameters' => [
                ['attachment' => new UploadedFile(self::TEXT_FILE, 22, UPLOAD_ERR_CANT_WRITE, 'resume.txt')],
                ['attachment' => new File(uploadFailedMessage: 'Property - {property}, file - {file}, error - {error}.')],
                ['attachment' => ['Property - attachment, file - resume.txt, error - 7.']],
            ],
            'windows style upload filename in error message' => [
                ['attachment' => new UploadedFile(self::TEXT_FILE, 22, UPLOAD_ERR_CANT_WRITE, 'C:\\temp\\resume.txt')],
                ['attachment' => new File(uploadFailedMessage: 'Property - {property}, file - {file}, error - {error}.')],
                ['attachment' => ['Property - attachment, file - resume.txt, error - 7.']],
            ],
            'object providing rules, property labels and wrong data' => [
                new class implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public string $file = FileTest::TEXT_FILE,
                    ) {}

                    public function getPropertyLabels(): array
                    {
                        return [
                            'file' => 'Файл',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'file' => [
                                new File(
                                    mimeTypes: ['image/jpeg'],
                                    wrongMimeTypeMessage: '{property} имеет неверный MIME-тип: {mimeTypes}.',
                                ),
                            ],
                        ];
                    }
                },
                null,
                ['file' => ['Файл имеет неверный MIME-тип: image/jpeg.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new File(), new File(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new File(), new File(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [File::class, FileHandler::class];
    }

    protected function getRuleClass(): string
    {
        return File::class;
    }

    private static function createStreamUpload(
        ?string $fileName,
        ?string $clientMediaType,
        ?int $size = 22,
    ): UploadedFile {
        $resource = fopen('php://temp', 'rb+');
        fwrite($resource, "Quarterly notes draft\n");
        rewind($resource);

        return new UploadedFile($resource, $size, UPLOAD_ERR_OK, $fileName, $clientMediaType);
    }
}
