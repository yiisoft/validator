<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Validator\Rule\Mime;

/**
 * @group validators
 */
class MimeTest extends TestCase
{
    public function testValidateString()
    {
        $val = Mime::rule(['image/jpeg']);
        $this->assertFalse($val->validate(null)->isValid());
        $this->assertFalse($val->validate('image/png')->isValid());
        $this->assertTrue($val->validate('image/jpeg')->isValid());

        $val = Mime::rule(['image/jpeg', 'image/png']);
        $this->assertTrue($val->validate('image/png')->isValid());
    }

    public function testValidateUploadedFile()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientMediaType')->willReturn('image/png');

        $val = Mime::rule(['image/jpeg']);
        $this->assertFalse($val->validate($file)->isValid());

        $val = Mime::rule(['image/jpeg', 'image/png']);
        $this->assertTrue($val->validate($file)->isValid());
    }

    public function testName(): void
    {
        $this->assertEquals('mime', Mime::rule([])->getName());
    }
}
