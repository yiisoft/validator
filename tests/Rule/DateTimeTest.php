<?php
namespace Yiisoft\Validator\Tests\Rule;

use IntlDateFormatter;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\DateTime;
use Yiisoft\Validator\Tests\data\FakedValidationModel;
use Yiisoft\Validator\Tests\IntlTestHelper;

/**
 * @group validators
 */
class DateTimeTest extends TestCase
{
    /**
     * @var array Default timeZone and locale settings for the validator
     */
    public $params = [
        'format' => 'd/m/Y H:i:s',
        'timeZone' => 'UTC',
        'locale' => 'ru-RU',
    ];

    protected function setUp()
    {
        parent::setUp();

        IntlTestHelper::setIntlStatus($this);
    }

    protected function tearDown()
    {
        parent::tearDown();
        IntlTestHelper::resetIntlStatus();
    }

    public function testEnsureMessageIsSet()
    {
        $val = new DateTime($this->params['format'], $this->params['locale'], $this->params['timeZone']);

        $reflection = new \ReflectionObject($val);
        $prop = $reflection->getProperty('_message');
        $prop->setAccessible(true);

        $message = ($prop->getValue($val));

        $this->assertTrue($message !== null && strlen($message) > 1);
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testIntlValidate($timezone)
    {
        date_default_timezone_set($timezone);
        $this->testValidate($timezone);

        $val = new DateTime('full', 'en-GB', $this->params['timeZone']);
        $this->assertTrue($val->validate('Friday, 25 October 2019 at 14:10:00 +06')->isValid());
        $this->assertFalse($val->validate('Friday, 25 October 2019 at 25:10:00 +06')->isValid());

        $val = new DateTime('full', 'de-DE', $this->params['timeZone']);
        $this->assertTrue($val->validate('Freitag, 25. Oktober 2019 um 14:10:00 +06')->isValid());
        $this->assertFalse($val->validate('Freitag, 25 Oktober 2019 um 14:10:00 +06')->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidate($timezone)
    {
        date_default_timezone_set($timezone);

        // test PHP format
        $val = new DateTime('php:Y-m-d H:i:s', $this->params['locale'], $timezone);
        $this->assertFalse($val->validate('3232-32-32')->isValid());
        $this->assertTrue($val->validate('2013-09-13 14:10:00')->isValid());
        $this->assertFalse($val->validate('31.7.2013')->isValid());
        $this->assertFalse($val->validate('31-7-2013')->isValid());
        $this->assertFalse($val->validate('20121212')->isValid());
        $this->assertFalse($val->validate('asdasdfasfd')->isValid());
        $this->assertFalse($val->validate('2012-12-12foo')->isValid());
        $this->assertFalse($val->validate('')->isValid());
        $this->assertFalse($val->validate(time())->isValid());
        $val->format('php:U');
        $this->assertTrue($val->validate(time())->isValid());
        $val->format('php:d.m.Y g:i:s A');
        $this->assertTrue($val->validate('31.7.2013 11:59:59 PM')->isValid());
        $val->format('php:Y-m-!d H:i:s');
        $this->assertTrue($val->validate('2009-02-15 15:16:17')->isValid());

        // test ICU format
        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $timezone);
        $this->assertFalse($val->validate('3232-32-32')->isValid());
        $this->assertTrue($val->validate('2013-09-13 16:25:01')->isValid());
        $this->assertFalse($val->validate('31.7.2013')->isValid());
        $this->assertFalse($val->validate('31-7-2013')->isValid());
        $this->assertFalse($val->validate('20121212')->isValid());
        $this->assertFalse($val->validate('asdasdfasfd')->isValid());
        $this->assertFalse($val->validate('2012-12-12foo')->isValid());
        $this->assertFalse($val->validate('')->isValid());
        $this->assertFalse($val->validate(time())->isValid());
        $val->format('dd.MM.yyyy h:mm a');
        $this->assertTrue($val->validate('31.7.2013 4:25 pm')->isValid());
        $val->format('yyyy-MM-dd HH:mm:ss');
        $this->assertTrue($val->validate('2009-02-15 15:16:17')->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testIntlValidateAttributePHPFormat($timezone)
    {
        $this->testValidateAttributePHPFormat($timezone);
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidateAttributePHPFormat($timezone)
    {
        date_default_timezone_set($timezone);

        // error-array-add
        $val = new DateTime('php:Y-m-d H:i:s', $this->params['locale'], $timezone);
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 23:59:59';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $model = new FakedValidationModel();
        $model->attr_date = '1375293913';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        //// timestamp attribute
        $val = (new DateTime('php:Y-m-d H:i:s', $this->params['locale'], $timezone));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 23:59:59';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        // array value
        $val = (new DateTime('php:Y-m-d H:i:s', $this->params['locale'], $timezone));
        $model = FakedValidationModel::createWithAttributes(['attr_date' => ['2013-09-13 23:59:59']]);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testIntlValidateAttributeICUFormat($timezone)
    {
        $this->testValidateAttributeICUFormat($timezone);
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidateAttributeICUFormat($timezone)
    {
        date_default_timezone_set($timezone);

        // error-array-add
        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $timezone);
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 23:59:59';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $model = new FakedValidationModel();
        $model->attr_date = '1375293913';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        //// timestamp attribute
        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $timezone));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 23:59:59';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        // array value
        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $timezone);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => ['2013-09-13 23:59:59']]);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        // invalid format
        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $timezone);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '2012-12-12foo']);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
    }

    public function testIntlMultibyteString()
    {
        $val = new DateTime('dd MMM yyyy HH:mm:ss', 'de_DE', $this->params['timeZone']);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '12 Mai 2014 14:10:00']);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('dd MMM yyyy HH:mm:ss', 'ru_RU', $this->params['timeZone']);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '12 мая 2014 14:10:00']);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
    }

    public function provideTimezones()
    {
        return [
            ['UTC'],
            ['Europe/Berlin'],
            ['America/Jamaica'],
        ];
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testIntlValidationWithTime($timezone)
    {
        // prepare data for specific ICU version, see https://github.com/yiisoft/yii2/issues/15140
        switch (true) {
            case (version_compare(INTL_ICU_VERSION, '57.1', '>=')):
            case (INTL_ICU_VERSION === '55.1'):
                $enGB_dateTime_valid = '31/05/2017, 12:30';
                $enGB_dateTime_invalid = '05/31/2017, 12:30';
                $deDE_dateTime_valid = '31.05.2017, 12:30';
                $deDE_dateTime_invalid = '05.31.2017, 12:30';
                break;
            default:
                $enGB_dateTime_valid = '31/5/2017 12:30';
                $enGB_dateTime_invalid = '5/31/2017 12:30';
                $deDE_dateTime_valid = '31.5.2017 12:30';
                $deDE_dateTime_invalid = '5.31.2017 12:30';
        }

        $this->testValidationWithTime($timezone);

        $locale = 'en-GB';
        $dateTimeFormat = 'short';

        $val = new DateTime($dateTimeFormat, $locale, $timezone);
        $this->assertTrue($val->validate($enGB_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($enGB_dateTime_invalid)->isValid());
        $val = new DateTime('short', 'en-GB', $timezone);
        $this->assertTrue($val->validate($enGB_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($enGB_dateTime_invalid)->isValid());

        $locale = 'de-DE';
        $val = new DateTime($dateTimeFormat, $locale, $timezone);
        $this->assertTrue($val->validate($deDE_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($deDE_dateTime_invalid)->isValid());

        $this->assertTrue($val->validate($deDE_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($deDE_dateTime_invalid)->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidationWithTime($timezone)
    {
        date_default_timezone_set($timezone);

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val =(new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'));
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testIntlValidationWithTimeAndOutputTimeZone($timezone)
    {
        $this->testValidationWithTime($timezone);
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidationWithTimeAndOutputTimeZone($timezone)
    {
        date_default_timezone_set($timezone);

        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('php:Y-m-d H:i:s', $this->params['locale'], 'UTC');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('php:Y-m-d H:i:s', $this->params['locale'], 'Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new DateTime('php:Y-m-d H:i:s', $this->params['locale'], 'Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
    }

    public function testIntlValidateRange()
    {
        $this->testvalidateRange();
    }

    public function testvalidateRange()
    {
        if (PHP_INT_SIZE == 8) { // this passes only on 64bit systems
            // intl parser allows 14 for yyyy pattern, see the following for more details:
            // https://github.com/yiisoft/yii2/blob/a003a8fb487dfa60c0f88ecfacf18a7407ced18b/framework/validators/DateValidator.php#L51-L57
            $date = '14-09-13 16:35:00';
            $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']);
            $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

            $min = '1900-01-01 12:00:00';
            $beforeMin = '1899-12-31 12:00:00';
        } else {
            $min = '1920-01-01 12:00:00';
            $beforeMin = '1919-12-31 12:00:00';
        }

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))->min($min);
        $date = '1958-01-01 13:00:00';
        $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))->max('2000-01-01 12:00:00');
        $date = '2000-01-01 12:00:01';
        $this->assertFalse($val->validate($date)->isValid(), "$date is too big");
        $date = '1958-01-12 13:00:00';
        $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))->min($min)
            ->max('2000-01-01 12:00:00');
        $this->assertTrue($val->validate('2000-01-01 11:59:59')->isValid(), 'max -1 sec is valid');
        $this->assertTrue($val->validate('2000-01-01 12:00:00')->isValid(), 'max is inside range');
        $this->assertTrue($val->validate($min)->isValid(), 'min is inside range');
        $this->assertFalse($val->validate($beforeMin)->isValid(), 'min -1 day is invalid');
        $this->assertFalse($val->validate('2000-01-01 12:00:01')->isValid(), 'max +1 sec is invalid');
    }

    private function validateModelAttribute($validator, $date, $expected, $message = '')
    {
        $model = new FakedValidationModel();
        $model->attr_date = $date;
        $result = $validator->validateAttribute($model, 'attr_date');
        if (!$expected) {
            $this->assertFalse($result->isValid(), $message);
        } else {
            $this->assertTrue($result->isValid(), $message);
        }
    }

    public function testIntlValidateAttributeRange()
    {
        $this->testValidateAttributeRange();
    }

    public function testValidateAttributeRange()
    {
        if (PHP_INT_SIZE == 8) { // this passes only on 64bit systems
            // intl parser allows 14 for yyyy pattern, see the following for more details:
            // https://github.com/yiisoft/yii2/blob/a003a8fb487dfa60c0f88ecfacf18a7407ced18b/framework/validators/DateValidator.php#L51-L57
            $val = new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']);
            $date = '14-09-13 12:00:00';
            $this->validateModelAttribute($val, $date, true, "$date is valid");

            $min = '1900-01-01 12:00:00';
            $beforeMin = '1899-12-31 12:00:00';
        } else {
            $min = '1920-01-01 12:00:00';
            $beforeMin = '1919-12-31 12:00:00';
        }

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))->min($min);
        $date = '1958-01-12 13:00:00';
        $this->validateModelAttribute($val, $date, true, "$date is valid");

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))
            ->max('2000-01-01 12:00:00');
        $date = '2000-01-01 12:00:01';
        $this->validateModelAttribute($val, $date, false, "$date is too big");
        $date = '1958-01-12 12:00:00';
        $this->validateModelAttribute($val, $date, true, "$date is valid");

        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))
            ->min($min)->max('2000-01-01 12:00:00');
        $this->validateModelAttribute($val, '2000-01-01 11:59:59', true, 'max -1 sec is valid');
        $this->validateModelAttribute($val, '2000-01-01 12:00:00', true, 'max is inside range');
        $this->validateModelAttribute($val, $min, true, 'min is inside range');
        $this->validateModelAttribute($val, $beforeMin, false, 'min -1 day is invalid');
        $this->validateModelAttribute($val, '2000-01-01 12:00:01', false, 'max +1 sec is invalid');
    }

    public function testIntlvalidateRangeOld()
    {
        if ($this->checkOldIcuBug()) {
            $this->markTestSkipped('ICU is too old.');
        }
        $date = '14-09-13 12:00:00';
        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))
            ->min('1920-01-01 12:00:00');
        $this->assertFalse($val->validate($date)->isValid(), "$date is too small");
    }

    public function testIntlValidateAttributeRangeOld()
    {
        if ($this->checkOldIcuBug()) {
            $this->markTestSkipped('ICU is too old.');
        }
        $date = '14-09-13 12:00:00';
        $val = (new DateTime('yyyy-MM-dd HH:mm:ss', $this->params['locale'], $this->params['timeZone']))
            ->min('1920-01-01 12:00:00');
        $this->validateModelAttribute($val, $date, false, "$date is too small");
    }

    /**
     * Returns true if the version of ICU is old and has a bug that makes it
     * impossible to parse two digit years properly.
     * @see http://bugs.icu-project.org/trac/ticket/9836
     * @return bool
     */
    private function checkOldIcuBug()
    {
        $date = '14';
        $formatter = new IntlDateFormatter('en-US', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'yyyy');
        $parsePos = 0;
        $parsedDate = @$formatter->parse($date, $parsePos);

        if (is_int($parsedDate) && $parsedDate > 0) {
            return true;
        }

        return false;
    }

//    /**
//     * @depends testValidateAttributePHPFormat
//     */
//    public function testTimestampAttributeSkipValidation()
//    {
//        // timestamp as integer
//        $val = (new DateTime('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
//            ->timestampAttribute('attr_date');
//        $model = new FakedValidationModel();
//        $model->attr_date = 1379030400;
//        $result = $val->validateAttribute($model, 'attr_date');
//        $this->assertTrue($result->isValid());
//
//        $val = (new DateTime('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
//            ->timestampAttribute('attr_date');
//        $model = new FakedValidationModel();
//        $model->attr_date = 'invalid';
//        $result = $val->validateAttribute($model, 'attr_date');
//        $this->assertFalse($result->isValid());
//
//        // timestamp as formatted date
//        $val = (new DateTime('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
//            ->timestampAttribute('attr_date')->timestampAttributeFormat('php:Y-m-d');
//        $model = new FakedValidationModel();
//        $model->attr_date = '2013-09-13';
//        $result = $val->validateAttribute($model, 'attr_date');
//        $this->assertTrue($result->isValid());
//
//        $val = (new DateTime('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
//            ->timestampAttribute('attr_date')->timestampAttributeFormat('php:Y-m-d');
//        $model = new FakedValidationModel();
//        $model->attr_date = '2013-09-2013';
//        $result = $val->validateAttribute($model, 'attr_date');
//        $this->assertFalse($result->isValid());
//    }

    /**
     * @depends testValidateAttributePHPFormat
     */
    public function testAttributeOnEmpty()
    {
        $validator = (new DateTime('php:Y/m/d H:i:s', $this->params['locale'], $this->params['timeZone']))
            ->skipOnEmpty(false);
        $model = new FakedValidationModel();
        $model->attr_date = '';
        $result = $validator->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
    }

    /**
     * Tests that DateValidator with format `php:U` does not truncate timestamp to date.
     * @see https://github.com/yiisoft/yii2/issues/15628
     */
    public function testIssue15628()
    {
        $validator = new DateTime('php:U', $this->params['locale'], $this->params['timeZone']);
        $model = new FakedValidationModel();
        $value = 1518023610;
        $model->attr_date = $value;

        $validator->validateAttribute($model, 'attr_date');

        $this->assertEquals($value, $model->attr_date);
    }
}
