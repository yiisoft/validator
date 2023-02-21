<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Helper;

use function array_key_exists;
use function is_array;

final class OptionsHelper
{
    public static function filterRecursive(&$array, array $keptOptionNames)
    {
        $hasKeptOptionName = false;
        foreach ($keptOptionNames as $keptOptionName) {
            if (array_key_exists($keptOptionName, $array)) {
                $hasKeptOptionName = true;

                break;
            }
        }

        $removedOptionNames = $hasKeptOptionName
            ? array_diff(array_keys($array), $keptOptionNames)
            : [];
        foreach ($removedOptionNames as $removedOptionName) {
            if ($removedOptionName !== 0) {
                unset($array[$removedOptionName]);
            }
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                self::filterRecursive($value, $keptOptionNames);
            }
        }
    }
}
