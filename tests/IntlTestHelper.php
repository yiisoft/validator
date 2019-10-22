<?php
namespace Yiisoft\Validator\Tests;

class IntlTestHelper
{
    public static $enableIntl;
    /**
     * Emulate disabled intl extension.
     *
     * Enable it only for tests prefixed with testIntl.
     * @param Testcase $test
     */
    public static function setIntlStatus($test)
    {
        static::$enableIntl = null;
        if (strncmp($test->getName(false), 'testIntl', 8) === 0) {
            static::$enableIntl = true;
            if (!extension_loaded('intl')) {
                $test->markTestSkipped('intl extension is not installed.');
            }
        } else {
            static::$enableIntl = false;
        }
    }
    public static function resetIntlStatus()
    {
        static::$enableIntl = null;
    }
}
