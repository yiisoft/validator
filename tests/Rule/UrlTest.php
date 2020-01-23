<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Url;

/**
 * @group validators
 */
class UrlTest extends TestCase
{
    public function testvalidate()
    {
        $val = new Url();
        $this->assertFalse($val->validate('google.de')->isValid());
        $this->assertTrue($val->validate('http://google.de')->isValid());
        $this->assertTrue($val->validate('https://google.de')->isValid());
        $this->assertFalse($val->validate('htp://yiiframework.com')->isValid());
        $this->assertTrue($val->validate('https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8'
                                        . '&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr')->isValid());
        $this->assertFalse($val->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertFalse($val->validate('http://invalid,domain')->isValid());
        $this->assertFalse($val->validate('http://example.com,')->isValid());
        $this->assertFalse($val->validate('http://example.com*12')->isValid());
        $this->assertTrue($val->validate('http://example.com/*12')->isValid());
        $this->assertTrue($val->validate('http://example.com/?test')->isValid());
        $this->assertTrue($val->validate('http://example.com/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:80/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:65535/#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81/?good')->isValid());
        $this->assertTrue($val->validate('http://example.com?test')->isValid());
        $this->assertTrue($val->validate('http://example.com#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81#test')->isValid());
        $this->assertTrue($val->validate('http://example.com:81?good')->isValid());
        $this->assertFalse($val->validate('http://example.com,?test')->isValid());
        $this->assertFalse($val->validate('http://example.com:?test')->isValid());
        $this->assertFalse($val->validate('http://example.com:test')->isValid());
        $this->assertFalse($val->validate('http://example.com:123456/test')->isValid());

        $this->assertFalse($val->validate('http://äüö?=!"§$%&/()=}][{³²€.edu')->isValid());
    }

    public function testvalidateWithoutScheme()
    {
        $val = (new Url())
            ->pattern('/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i');

        $this->assertTrue($val->validate('yiiframework.com')->isValid());
    }

    public function testValidateWithCustomScheme()
    {
        $val = (new Url())
            ->schemes(['http', 'https', 'ftp', 'ftps']);

        $this->assertTrue($val->validate('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertTrue($val->validate('http://google.de')->isValid());
        $this->assertTrue($val->validate('https://google.de')->isValid());
        $this->assertFalse($val->validate('htp://yiiframework.com')->isValid());
        // relative urls not supported
        $this->assertFalse($val->validate('//yiiframework.com')->isValid());
    }

    public function testValidateWithIdn()
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');

            return;
        }
        $val = (new Url())
            ->enableIDN();

        $this->assertTrue($val->validate('http://äüößìà.de')->isValid());
        // converted via http://mct.verisign-grs.com/convertServlet
        $this->assertTrue($val->validate('http://xn--zcack7ayc9a.de')->isValid());
    }

    public function testValidateLength()
    {
        $url = 'http://' . str_pad('base', 2000, 'url') . '.de';
        $val = new Url();
        $this->assertFalse($val->validate($url)->isValid());
    }
}
