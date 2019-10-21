<?php
namespace Yiisoft\Validator\Tests\Rule;

use IntlDateFormatter;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Date;
use Yiisoft\Validator\Tests\data\FakedValidationModel;
use Yiisoft\Validator\Tests\IntlTestHelper;

/**
 * @group validators
 */
class DateTest extends TestCase
{
    /**
     * @var array Default timeZone and locale settings for the validator
     */
    public $params = [
        'format' => 'd/m/Y',
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
        $val = new Date($this->params['format'], $this->params['locale'], $this->params['timeZone']);

        $reflection = new \ReflectionObject($val);
        $prop = $reflection->getProperty('message');
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

        $val = new Date('short', 'en-GB', $this->params['timeZone']);
        $this->assertTrue($val->validate('31/5/2017')->isValid());
        $this->assertFalse($val->validate('5/31/2017')->isValid());

        $val = new Date('short', 'de-DE', $this->params['timeZone']);
        $this->assertTrue($val->validate('31.5.2017')->isValid());
        $this->assertFalse($val->validate('5.31.2017')->isValid());
    }

    /**
     * @dataProvider provideTimezones
     * @param string $timezone
     */
    public function testValidate($timezone)
    {
        date_default_timezone_set($timezone);

        // test PHP format
        $val = new Date('php:Y-m-d', $this->params['locale'], $timezone);
        $this->assertFalse($val->validate('3232-32-32')->isValid());
        $this->assertTrue($val->validate('2013-09-13')->isValid());
        $this->assertFalse($val->validate('31.7.2013')->isValid());
        $this->assertFalse($val->validate('31-7-2013')->isValid());
        $this->assertFalse($val->validate('20121212')->isValid());
        $this->assertFalse($val->validate('asdasdfasfd')->isValid());
        $this->assertFalse($val->validate('2012-12-12foo')->isValid());
        $this->assertFalse($val->validate('')->isValid());
        $this->assertFalse($val->validate(time())->isValid());
        $val->format = 'php:U';
        $this->assertTrue($val->validate(time())->isValid());
        $val->format = 'php:d.m.Y';
        $this->assertTrue($val->validate('31.7.2013')->isValid());
        $val->format = 'php:Y-m-!d H:i:s';
        $this->assertTrue($val->validate('2009-02-15 15:16:17')->isValid());

        // test ICU format
        $val = new Date('yyyy-MM-dd', $this->params['locale'], $timezone);
        $this->assertFalse($val->validate('3232-32-32')->isValid());
        $this->assertTrue($val->validate('2013-09-13')->isValid());
        $this->assertFalse($val->validate('31.7.2013')->isValid());
        $this->assertFalse($val->validate('31-7-2013')->isValid());
        $this->assertFalse($val->validate('20121212')->isValid());
        $this->assertFalse($val->validate('asdasdfasfd')->isValid());
        $this->assertFalse($val->validate('2012-12-12foo')->isValid());
        $this->assertFalse($val->validate('')->isValid());
        $this->assertFalse($val->validate(time())->isValid());
        $val->format = 'dd.MM.yyyy';
        $this->assertTrue($val->validate('31.7.2013')->isValid());
        $val->format = 'yyyy-MM-dd HH:mm:ss';
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
        $val = new Date('php:Y-m-d', $this->params['locale'], $timezone);
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $model = new FakedValidationModel();
        $model->attr_date = '1375293913';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        //// timestamp attribute
        $val = (new Date('php:Y-m-d', $this->params['locale'], $timezone))->timestampAttribute('attr_timestamp');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertEquals(
            1379030400, // 2013-09-13 00:00:00
            $model->attr_timestamp
        );
        // array value
        $val = (new Date('php:Y-m-d', $this->params['locale'], $timezone))->timestampAttribute('attr_timestamp');
        $model = FakedValidationModel::createWithAttributes(['attr_date' => ['2013-09-13']]);
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
        $val = new Date('yyyy-MM-dd', $this->params['locale'], $timezone);
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $model = new FakedValidationModel();
        $model->attr_date = '1375293913';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        //// timestamp attribute
        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $timezone))->timestampAttribute('attr_timestamp');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame(
            1379030400, // 2013-09-13 00:00:00
            $model->attr_timestamp
        );
        // array value
        $val = new Date('yyyy-MM-dd', $this->params['locale'], $timezone);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => ['2013-09-13']]);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
        // invalid format
        $val = new Date('yyyy-MM-dd', $this->params['locale'], $timezone);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '2012-12-12foo']);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
    }

    public function testIntlMultibyteString()
    {
        $val = new Date('dd MMM yyyy', 'de_DE', $this->params['timeZone']);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '12 Mai 2014']);
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = new Date('dd MMM yyyy', 'ru_RU', $this->params['timeZone']);
        $model = FakedValidationModel::createWithAttributes(['attr_date' => '12 мая 2014']);
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

    public function timestampFormatProvider()
    {
        $return = [];
        foreach ($this->provideTimezones() as $appTz) {
            foreach ($this->provideTimezones() as $tz) {
                $return[] = ['yyyy-MM-dd', '2013-09-13', '2013-09-13', $tz[0], $appTz[0]];
                // regardless of timezone, a simple date input should always result in 00:00:00 time
                $return[] = ['yyyy-MM-dd HH:mm:ss', '2013-09-13', '2013-09-13 00:00:00', $tz[0], $appTz[0]];
                $return[] = ['php:Y-m-d', '2013-09-13', '2013-09-13', $tz[0], $appTz[0]];
                $return[] = ['php:Y-m-d H:i:s', '2013-09-13', '2013-09-13 00:00:00', $tz[0], $appTz[0]];
                $return[] = ['php:U', '2013-09-13', '1379030400', $tz[0], $appTz[0]];
                $return[] = [null, '2013-09-13', 1379030400, $tz[0], $appTz[0]];
            }
        }

        return $return;
    }

    /**
     * @dataProvider timestampFormatProvider
     * @param string|null $format
     * @param string $date
     * @param string|int $expectedDate
     * @param string $timezone
     * @param string $appTimezone
     */
    public function testIntlTimestampAttributeFormat($format, $date, $expectedDate, $timezone, $appTimezone)
    {
        $this->testTimestampAttributeFormat($format, $date, $expectedDate, $timezone, $appTimezone);
    }

    /**
     * @dataProvider timestampFormatProvider
     * @param string|null $format
     * @param string $date
     * @param string|int $expectedDate
     * @param string $timezone
     * @param string $appTimezone
     */
    public function testTimestampAttributeFormat($format, $date, $expectedDate, $timezone, $appTimezone)
    {
        date_default_timezone_set($timezone);

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $appTimezone))->timestampAttribute('attr_timestamp')->timestampAttributeFormat($format);
        $model = new FakedValidationModel();
        $model->attr_date = $date;
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame($expectedDate, $model->attr_timestamp);
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

        $val = (new Date($dateTimeFormat, $locale, $timezone))->type(Date::TYPE_DATETIME);
        $this->assertTrue($val->validate($enGB_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($enGB_dateTime_invalid)->isValid());
        $val = (new Date('short', 'en-GB', $timezone))->type(Date::TYPE_DATETIME);
        $this->assertTrue($val->validate($enGB_dateTime_valid)->isValid());
        $this->assertFalse($val->validate($enGB_dateTime_invalid)->isValid());

        $locale = 'de-DE';
        $val = (new Date($dateTimeFormat, $locale, $timezone))->type(Date::TYPE_DATETIME);
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

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'))
            ->timestampAttribute('attr_timestamp');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame(1379082195, $model->attr_timestamp);

        $val =(new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'))->timestampAttribute('attr_timestamp');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame(1379082195, $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'))->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 14:23:15', $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'))->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 14:23:15', $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'))->timestampAttribute('attr_timestamp')->timestampAttributeFormat('php:Y-m-d H:i:s');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 14:23:15', $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'))->timestampAttribute('attr_timestamp')->timestampAttributeFormat('php:Y-m-d H:i:s');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 14:23:15', $model->attr_timestamp);
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

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'UTC'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 16:23:15', $model->attr_timestamp);
        $val = (new Date('php:Y-m-d H:i:s', $this->params['locale'], 'UTC'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 14:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 16:23:15', $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 16:23:15', $model->attr_timestamp);
        $val = (new Date('php:Y-m-d H:i:s', $this->params['locale'], 'Europe/Berlin'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('Europe/Berlin');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 16:23:15', $model->attr_timestamp);

        $val = (new Date('yyyy-MM-dd HH:mm:ss', $this->params['locale'], 'Europe/Berlin'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('America/New_York');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 10:23:15', $model->attr_timestamp);
        $val = (new Date('php:Y-m-d H:i:s', $this->params['locale'], 'Europe/Berlin'))
            ->timestampAttribute('attr_timestamp')->timestampAttributeFormat('yyyy-MM-dd HH:mm:ss')
            ->timestampAttributeTimeZone('America/New_York');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13 16:23:15';
        $model->attr_timestamp = true;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertSame('2013-09-13 10:23:15', $model->attr_timestamp);
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
            $date = '14-09-13';
            $val = new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']);
            $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

            $min = '1900-01-01';
            $beforeMin = '1899-12-31';
        } else {
            $min = '1920-01-01';
            $beforeMin = '1919-12-31';
        }

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))->min($min);
        $date = '1958-01-12';
        $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))->max('2000-01-01');
        $date = '2014-09-13';
        $this->assertFalse($val->validate($date)->isValid(), "$date is too big");
        $date = '1958-01-12';
        $this->assertTrue($val->validate($date)->isValid(), "$date is valid");

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))->min($min)
            ->max('2000-01-01');
        $this->assertTrue($val->validate('1999-12-31')->isValid(), 'max -1 day is valid');
        $this->assertTrue($val->validate('2000-01-01')->isValid(), 'max is inside range');
        $this->assertTrue($val->validate($min)->isValid(), 'min is inside range');
        $this->assertFalse($val->validate($beforeMin)->isValid(), 'min -1 day is invalid');
        $this->assertFalse($val->validate('2000-01-02')->isValid(), 'max +1 day is invalid');
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
            $val = new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']);
            $date = '14-09-13';
            $this->validateModelAttribute($val, $date, true, "$date is valid");

            $min = '1900-01-01';
            $beforeMin = '1899-12-31';
        } else {
            $min = '1920-01-01';
            $beforeMin = '1919-12-31';
        }

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))->min($min);
        $date = '1958-01-12';
        $this->validateModelAttribute($val, $date, true, "$date is valid");

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))
            ->max('2000-01-01');
        $date = '2014-09-13';
        $this->validateModelAttribute($val, $date, false, "$date is too big");
        $date = '1958-01-12';
        $this->validateModelAttribute($val, $date, true, "$date is valid");

        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))
            ->min($min)->max('2000-01-01');
        $this->validateModelAttribute($val, '1999-12-31', true, 'max -1 day is valid');
        $this->validateModelAttribute($val, '2000-01-01', true, 'max is inside range');
        $this->validateModelAttribute($val, $min, true, 'min is inside range');
        $this->validateModelAttribute($val, $beforeMin, false, 'min -1 day is invalid');
        $this->validateModelAttribute($val, '2000-01-02', false, 'max +1 day is invalid');
    }

    public function testIntlvalidateRangeOld()
    {
        if ($this->checkOldIcuBug()) {
            $this->markTestSkipped('ICU is too old.');
        }
        $date = '14-09-13';
        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))
            ->min('1920-01-01');
        $this->assertFalse($val->validate($date)->isValid(), "$date is too small");
    }

    public function testIntlValidateAttributeRangeOld()
    {
        if ($this->checkOldIcuBug()) {
            $this->markTestSkipped('ICU is too old.');
        }
        $date = '14-09-13';
        $val = (new Date('yyyy-MM-dd', $this->params['locale'], $this->params['timeZone']))
            ->min('1920-01-01');
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

    /**
     * @depends testValidateAttributePHPFormat
     */
    public function testTimestampAttributeSkipValidation()
    {
        // timestamp as integer
        $val = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_date');
        $model = new FakedValidationModel();
        $model->attr_date = 1379030400;
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_date');
        $model = new FakedValidationModel();
        $model->attr_date = 'invalid';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());

        // timestamp as formatted date
        $val = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_date')->timestampAttributeFormat('php:Y-m-d');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-13';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());

        $val = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_date')->timestampAttributeFormat('php:Y-m-d');
        $model = new FakedValidationModel();
        $model->attr_date = '2013-09-2013';
        $result = $val->validateAttribute($model, 'attr_date');
        $this->assertFalse($result->isValid());
    }

    /**
     * @depends testValidateAttributePHPFormat
     */
    public function testTimestampAttributeOnEmpty()
    {
        $validator = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_date')->skipOnEmpty(false);
        $model = new FakedValidationModel();
        $model->attr_date = '';
        $result = $validator->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertNull($model->attr_date);

        $validator = (new Date('php:Y/m/d', $this->params['locale'], $this->params['timeZone']))
            ->timestampAttribute('attr_timestamp')->skipOnEmpty(false);
        $model = new FakedValidationModel();
        $model->attr_date = '';
        $model->attr_timestamp = 1379030400;
        $result = $validator->validateAttribute($model, 'attr_date');
        $this->assertTrue($result->isValid());
        $this->assertNull($model->attr_timestamp);
    }

    /**
     * Tests that DateValidator with format `php:U` does not truncate timestamp to date.
     * @see https://github.com/yiisoft/yii2/issues/15628
     */
    public function testIssue15628()
    {
        $validator = (new Date('php:U' , $this->params['locale'], $this->params['timeZone']))
            ->type(Date::TYPE_DATETIME)->timestampAttribute('attr_date');
        $model = new FakedValidationModel();
        $value = 1518023610;
        $model->attr_date = $value;

        $validator->validateAttribute($model, 'attr_date');

        $this->assertEquals($value, $model->attr_date);
    }
}
