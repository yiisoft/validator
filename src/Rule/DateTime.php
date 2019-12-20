<?php
namespace Yiisoft\Validator\Rule;

use DateTimeZone;
use Exception;
use IntlDateFormatter;
use Yiisoft\Validator\FormatConverterHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * DateTime rule verifies if the attribute represents a datetime in a proper [[format]].
 *
 * It can also parse internationalized dates in a specific [[locale]] like e.g. `12 мая 2014` when [[format]]
 * is configured to use a time pattern in ICU format.
 *
 * It is further possible to limit the date within a certain range using [[min]] and [[max]].
 *
 * Additional to validating the date it can also export the parsed timestamp as a machine readable format
 * which can be configured using [[timestampAttribute]]. For values that include time information (not date-only values)
 * also the time zone will be adjusted. The time zone of the input value is assumed to be the one specified by the [[timeZone]]
 * property and the target timeZone will be UTC when [[timestampAttributeFormat]] is `null` (exporting as UNIX timestamp)
 * or [[timestampAttributeTimeZone]] otherwise. If you want to avoid the time zone conversion, make sure that [[timeZone]] and
 * [[timestampAttributeTimeZone]] are the same.
 */
class DateTime extends Rule
{

    /**
     * @var string the datetime format that the value being validated should follow.
     * This can be a date time pattern as described in the [ICU manual](http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax).
     *
     * Alternatively this can be a string prefixed with `php:` representing a format that can be recognized by the PHP Datetime class.
     * Please refer to <http://php.net/manual/en/datetime.createfromformat.php> on supported formats.
     *
     * If this property is not set, the default value will be obtained from `Yii::getApp()->formatter->dateFormat`, see [[\yii\i18n\Formatter::dateFormat]] for details.
     * Since version 2.0.8 the default value will be determined from different formats of the formatter class,
     * dependent on the value of [[type]]:
     *
     * Tthe default value will be taken from [[\yii\i18n\Formatter::datetimeFormat]],
     *
     * Here are some example values:
     *
     * ```php
     * 'yyyy-MM-dd HH:mm:ss' // date in ICU format
     * 'php:Y-m-d H:i:s' // the same date in PHP format
     * ```
     *
     * **Note:** the underlying date parsers being used vary dependent on the format. If you use the ICU format and
     * the [PHP intl extension](http://php.net/manual/en/book.intl.php) is installed, the [IntlDateFormatter](http://php.net/manual/en/intldateformatter.parse.php)
     * is used to parse the input value. In all other cases the PHP [\DateTime](http://php.net/manual/en/datetime.createfromformat.php) class
     * is used. The IntlDateFormatter has the advantage that it can parse international dates like `12. Mai 2015` or `12 мая 2014`, while the
     * PHP parser is limited to English only. The PHP parser however is more strict about the input format as it will not accept
     * `12.05.05` for the format `php:d.m.Y`, but the IntlDateFormatter will accept it for the format `dd.MM.yyyy`.
     * If you need to use the IntlDateFormatter you can avoid this problem by specifying a [[min|minimum date]].
     */
    private $format;
    /**
     * @var string the locale ID that is used to localize the date parsing.
     * This is only effective when the [PHP intl extension](http://php.net/manual/en/book.intl.php) is installed.
     * If not set, the locale of the [[\yii\base\Application::formatter|formatter]] will be used.
     * See also [[\yii\i18n\Formatter::locale]].
     */
    private $locale;
    /**
     * @var string the timezone to use for parsing date and time values.
     * This can be any value that may be passed to [date_default_timezone_set()](http://www.php.net/manual/en/function.date-default-timezone-set.php)
     * e.g. `UTC`, `Europe/Berlin` or `America/Chicago`.
     * Refer to the [php manual](http://www.php.net/manual/en/timezones.php) for available timezones.
     * If this property is not set, [[\yii\base\Application::timeZone]] will be used.
     */
    private $timeZone;
    /**
     * @var int|string upper limit of the date. Defaults to null, meaning no upper limit.
     * This can be a unix timestamp or a string representing a date time value.
     * If this property is a string, [[format]] will be used to parse it.
     * @see tooBig for the customized message used when the date is too big.
     */
    private $max;
    /**
     * @var int|string lower limit of the date. Defaults to null, meaning no lower limit.
     * This can be a unix timestamp or a string representing a date time value.
     * If this property is a string, [[format]] will be used to parse it.
     * @see tooSmall for the customized message used when the date is too small.
     */
    private $min;
    /**
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    private $tooBig;
    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    private $tooSmall;
    /**
     * @var string user friendly value of upper limit to display in the error message.
     * If this property is null, the value of [[max]] will be used (before parsing).
     */
    private $maxString;
    /**
     * @var string user friendly value of lower limit to display in the error message.
     * If this property is null, the value of [[min]] will be used (before parsing).
     */
    private $minString;
    /**
     * @var string user-defined error message used when the value is invalid.
     */
    private $message;

    /**
     * @var array map of short format names to IntlDateFormatter constant values.
     */
    private $dateFormats = [
        'short' => 3, // IntlDateFormatter::SHORT,
        'medium' => 2, // IntlDateFormatter::MEDIUM,
        'long' => 1, // IntlDateFormatter::LONG,
        'full' => 0, // IntlDateFormatter::FULL,
    ];

    /**
     * Date constructor.
     * @param $format string date format
     * @param $locale string locale code in ICU format
     * @param $timeZone string timezone to use for parsing date and time values
     */
    public function __construct($format, $locale, $timeZone)
    {
        $this->message = $this->formatMessage('The format of {attribute} is invalid.');

        $this->format = $format;
        $this->locale = $locale;
        $this->timeZone = $timeZone;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $result = new Result();

        $value = $model->$attribute;
        if ($this->isEmpty($value)) {
            $result->addError($this->formatMessage('{attribute} is empty'));
            return $result;
        }

        $timestamp = $this->parseDateValue($value);
        if ($timestamp === false) {
            $result->addError($this->message);
        } elseif ($this->min !== null && $timestamp < $this->min) {
            $result->addError($this->formatMessage($this->tooSmall, ['min' => $this->minString]));
        } elseif ($this->max !== null && $timestamp > $this->max) {
            $result->addError($this->formatMessage($this->tooBig, ['max' => $this->maxString]));
        }

        return $result;
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        $timestamp = $this->parseDateValue($value);
        if ($timestamp === false) {
            $result->addError($this->message);
        } elseif ($this->min !== null && $timestamp < $this->min) {
            $result->addError($this->formatMessage($this->tooSmall, ['min' => $this->minString]));
        } elseif ($this->max !== null && $timestamp > $this->max) {
            $result->addError($this->formatMessage($this->tooBig, ['max' => $this->maxString]));
        }

        return $result;
    }

    /**
     * Parses date string into UNIX timestamp.
     *
     * @param string $value string representing date
     * @return int|false a UNIX timestamp or `false` on failure.
     */
    protected function parseDateValue($value)
    {
        // TODO consider merging these methods into single one at 2.1
        return $this->parseDateValueFormat($value, $this->format);
    }

    /**
     * Parses date string into UNIX timestamp.
     *
     * @param string $value string representing date
     * @param string $format expected date format
     * @return int|false a UNIX timestamp or `false` on failure.
     */
    private function parseDateValueFormat($value, $format)
    {
        if (is_array($value)) {
            return false;
        }
        if (strncmp($format, 'php:', 4) === 0) {
            $format = substr($format, 4);
        } else {
            if (extension_loaded('intl')) {
                return $this->parseDateValueIntl($value, $format);
            }

            // fallback to PHP if intl is not installed
            $format = FormatConverterHelper::convertDateIcuToPhp($format, 'date', $this->locale);
        }

        return $this->parseDateValuePHP($value, $format);
    }

    /**
     * Parses a date value using the IntlDateFormatter::parse().
     * @param string $value string representing date
     * @param string $format the expected date format
     * @return int|bool a UNIX timestamp or `false` on failure.
     * @throws Exception
     */
    private function parseDateValueIntl($value, $format)
    {
        if (isset($this->dateFormats[$format])) {
            $formatter = new IntlDateFormatter($this->locale, $this->dateFormats[$format], $this->dateFormats[$format], $this->timeZone);
        } else {
            // if no time was provided in the format string set time to 0 to get a simple date timestamp
            $hasTimeInfo = (strpbrk($format, 'ahHkKmsSA') !== false);
            $formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, $hasTimeInfo ? $this->timeZone : 'UTC', null, $format);
        }
        // enable strict parsing to avoid getting invalid date values
        $formatter->setLenient(false);

        // There should not be a warning thrown by parse() but this seems to be the case on windows so we suppress it here
        // See https://github.com/yiisoft/yii2/issues/5962 and https://bugs.php.net/bug.php?id=68528
        $parsePos = 0;
        $parsedDate = @$formatter->parse($value, $parsePos);
        if ($parsedDate === false || $parsePos !== mb_strlen($value)) {
            return false;
        }

        return $parsedDate;
    }

    /**
     * Parses a date value using the \DateTime::createFromFormat().
     * @param string $value string representing date
     * @param string $format the expected date format
     * @return int|bool a UNIX timestamp or `false` on failure.
     */
    private function parseDateValuePHP($value, $format)
    {
        // if no time was provided in the format string set time to 0 to get a simple date timestamp
        $hasTimeInfo = (strpbrk($format, 'HhGgisU') !== false);

        $date = \DateTime::createFromFormat($format, $value, new DateTimeZone($hasTimeInfo ? $this->timeZone : 'UTC'));
        $errors = \DateTime::getLastErrors();
        if ($date === false || $errors['error_count'] || $errors['warning_count']) {
            return false;
        }

        if (!$hasTimeInfo) {
            $date->setTime(0, 0, 0);
        }

        return $date->getTimestamp();
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function format($format): self
    {
        $this->format = $format;

        return $this;
    }

    public function min($value): self
    {
        $this->min = $value;
        if ($this->min !== null && $this->tooSmall === null) {
            $this->tooSmall = $this->formatMessage('{attribute} must be no less than {min}.');
        }

        if ($this->minString === null) {
            $this->minString = (string) $this->min;
        }
        if ($this->min !== null && is_string($this->min)) {
            $timestamp = $this->parseDateValue($this->min);
            if ($timestamp === false) {
                throw new Exception("Invalid min date value: {$this->min}");
            }
            $this->min = $timestamp;
        }

        return $this;
    }

    public function max($value): self
    {
        $this->max = $value;
        if ($this->max !== null && $this->tooBig === null) {
            $this->tooBig = $this->formatMessage('{attribute} must be no greater than {max}.');
        }
        if ($this->maxString === null) {
            $this->maxString = (string) $this->max;
        }
        if ($this->max !== null && is_string($this->max)) {
            $timestamp = $this->parseDateValue($this->max);
            if ($timestamp === false) {
                throw new Exception("Invalid max date value: {$this->max}");
            }
            $this->max = $timestamp;
        }

        return $this;
    }
}
