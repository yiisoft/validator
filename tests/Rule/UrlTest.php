<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Url;

/**
 * @group validators
 */
class UrlTest extends TestCase
{
    public function testValidateValue()
    {
        $val = new Url();
        $this->assertFalse($val->validateValue('google.de')->isValid());
        $this->assertTrue($val->validateValue('http://google.de')->isValid());
        $this->assertTrue($val->validateValue('https://google.de')->isValid());
        $this->assertFalse($val->validateValue('htp://yiiframework.com')->isValid());
        $this->assertTrue($val->validateValue('https://www.google.de/search?q=yii+framework&ie=utf-8&oe=utf-8'
                                        . '&rls=org.mozilla:de:official&client=firefox-a&gws_rd=cr')->isValid());
        $this->assertFalse($val->validateValue('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertFalse($val->validateValue('http://invalid,domain')->isValid());
        $this->assertFalse($val->validateValue('http://example.com,')->isValid());
        $this->assertFalse($val->validateValue('http://example.com*12')->isValid());
        $this->assertTrue($val->validateValue('http://example.com/*12')->isValid());
        $this->assertTrue($val->validateValue('http://example.com/?test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com/#test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com:80/#test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com:65535/#test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com:81/?good')->isValid());
        $this->assertTrue($val->validateValue('http://example.com?test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com#test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com:81#test')->isValid());
        $this->assertTrue($val->validateValue('http://example.com:81?good')->isValid());
        $this->assertFalse($val->validateValue('http://example.com,?test')->isValid());
        $this->assertFalse($val->validateValue('http://example.com:?test')->isValid());
        $this->assertFalse($val->validateValue('http://example.com:test')->isValid());
        $this->assertFalse($val->validateValue('http://example.com:123456/test')->isValid());

        $this->assertFalse($val->validateValue('http://äüö?=!"§$%&/()=}][{³²€.edu')->isValid());
    }

    public function testValidateValueWithDefaultScheme()
    {
        $val = new Url(['defaultScheme' => 'https']);
        $this->assertTrue($val->validateValue('yiiframework.com')->isValid());
        $this->assertTrue($val->validateValue('http://yiiframework.com')->isValid());
    }

    public function testValidateValueWithoutScheme()
    {
        $val = new Url(['pattern' => '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i']);
        $this->assertTrue($val->validateValue('yiiframework.com')->isValid());
    }

    public function testValidateWithCustomScheme()
    {
        $val = new Url([
            'validSchemes' => ['http', 'https', 'ftp', 'ftps'],
            'defaultScheme' => 'http',
        ]);
        $this->assertTrue($val->validateValue('ftp://ftp.ruhr-uni-bochum.de/')->isValid());
        $this->assertTrue($val->validateValue('google.de')->isValid());
        $this->assertTrue($val->validateValue('http://google.de')->isValid());
        $this->assertTrue($val->validateValue('https://google.de')->isValid());
        $this->assertFalse($val->validateValue('htp://yiiframework.com')->isValid());
        // relative urls not supported
        $this->assertFalse($val->validateValue('//yiiframework.com')->isValid());
    }

    public function testValidateWithIdn()
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl package required');

            return;
        }
        $val = new Url([
            'enableIDN' => true,
        ]);
        $this->assertTrue($val->validateValue('http://äüößìà.de')->isValid());
        // converted via http://mct.verisign-grs.com/convertServlet
        $this->assertTrue($val->validateValue('http://xn--zcack7ayc9a.de')->isValid());
    }

    public function testValidateLength()
    {
        $url = 'http://' . str_pad('base', 2000, 'url') . '.de';
        $val = new Url();
        $this->assertFalse($val->validateValue($url)->isValid());
    }

//    public function testValidateAttributeAndError()
//    {
//        $obj = new FakedValidationModel();
//        $obj->attr_url = 'http://google.de';
//        $val = new Url();
//        $val->validateAttribute($obj, 'attr_url');
//        $this->assertFalse($obj->hasErrors('attr_url'));
//        $this->assertSame('http://google.de', $obj->attr_url);
//        $obj = new FakedValidationModel();
//        $val->defaultScheme = 'http';
//        $obj->attr_url = 'google.de';
//        $val->validateAttribute($obj, 'attr_url');
//        $this->assertFalse($obj->hasErrors('attr_url'));
//        $this->assertNotFalse(stripos($obj->attr_url, 'http'));
//        $obj = new FakedValidationModel();
//        $obj->attr_url = 'gttp;/invalid string';
//        $val->validateAttribute($obj, 'attr_url');
//        $this->assertTrue($obj->hasErrors('attr_url'));
//    }
}
