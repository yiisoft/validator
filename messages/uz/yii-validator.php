<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\Rule\Json;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'The data must have at least "{min}" filled attributes.' => 'Kamida {min} ta atributlar toʻldirilgan boʻlishi kerak.',
    /** @see BooleanValue */
    'Value must be either "{true}" or "{false}".' => 'Qiymat "{true}" yoki "{false}" boʻlishi kerak.',
    /** @see Count */
    'This value must be an array or implement \Countable interface.' => 'Qiymat massiv yoki \Countable interfeysidan meros olingan boʻlishi kerak.',
    'This value must contain at least {min, number} {min, plural, one{item} other{items}}.' => 'Qiymat kamida {min, number} ta {min, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    'This value must contain at most {max, number} {max, plural, one{item} other{items}}.' => 'Qiymat koʻpi bilan {max, number} ta {max, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => 'Qiymat aynan {exactly, number} ta {exactly, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    /** @see Each */
    'Value must be array or iterable.' => 'Qiymat massiv yoki takrorlanadigan(iterable) psevdo-tur boʻlishi kerak',
    'Every iterable key must have an integer or a string type.' => 'Barcha kalit integer yoki string turida boʻlishi kerak.',
    /** @see Email */
    'This value is not a valid email address.' => 'Qiymat yaroqli elektron pochta manzili emas.',
    /** @see In */
    'This value is not in the list of acceptable values.' => 'Qiymat qabul qilinadigan qiymatlar roʻyxatida yoʻq.',
    /** @see Ip */
    'Must be a valid IP address.' => 'Toʻgʻri IP manzil boʻlishi kerak.',
    'Must not be an IPv4 address.' => 'IPv4 manzil boʻlmasligi kerak.',
    'Must not be an IPv6 address.' => 'IPv6 manzil boʻlmasligi kerak.',
    'Contains wrong subnet mask.' => 'Notoʻgʻri quyi tarmoq(subnet) niqobini oʻz ichiga olgan.',
    'Must be an IP address with specified subnet.' => 'Quyi tarmoq(subnet)ga ega IP manzil boʻlishi kerak.',
    'Must not be a subnet.' => 'Quyi tarmoq(subnet) boʻlmasligi kerak.',
    'Is not in the allowed range.' => 'Ruxsat etilgan manzillar qatoriga kirmaydi.',
    /** @see Integer */
    'Value must be an integer.' => 'Qiymat butun son boʻlishi kerak.',
    /** @see Json */
    'The value is not JSON.' => 'Qiymat JSON holatida emas.',
    /** @see Length */
    'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'Qiymat kamida {min, number} ta {min, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'Qiymat koʻpi bilan {max, number} ta {max, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => 'Qiymat {exactly, number} ta {exactly, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Qoidalarga ega boʻlmagan Nested qoidasi faqat obyektlarga ishlatilishi mumkin.',
    'An object data set data can only have an array or an object type.' => 'Obyekt maʼlumotlari massiv yoki obyekt boʻlishi kerak.',
    'Property "{path}" is not found.' => '{path} xususiyati topilmadi.',
    /** @see Number */
    'Value must be a number.' => 'Qiymat raqam boʻlishi kerak.',
    /** @see Regex */
    'Value is invalid.' => 'Qiymat notoʻgʻri.',
    /** @see Required */
    'Value cannot be blank.' => 'Qiymat boʻsh boʻlishi mumkin emas.',
    'Value not passed.' => 'Qiymat oʻtmadi.',
    /** @see Subset */
    'Value must be iterable.' => 'Qiymat takrorlanadigan boʻlishi kerak',
    'This value is not a subset of acceptable values.' => 'Bu qiymat ruxsat etilgan qiymatlarning quyi toʻplami emas.',
    /** @see TrueValue */
    'The value must be "{true}".' => 'Qiymat "{true}" boʻlishi kerak.',
    /** @see Url */
    'This value is not a valid URL.' => 'Qiymat yaroqli havola emas.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     */
    'The value must be an array or an object.' => 'Qiymat massiv yoki obyekt boʻlishi kerak.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Ruxsat berilgan turlar: integer, float, string, boolean va null. Bu qiymat turi {type}',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types are integer, float, string, boolean, null and object implementing \Stringable interface or \DateTimeInterface.' => 'Ruxsat berilgan turlar: integer, float, string, boolean va null.', // TODO: Actualize
    'The attribute value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Maʼlumotlar toʻplamidan olingan qiymat skalyar turdagi boʻlishi kerak.', // TODO: Actualize
    'Value must be equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"ga teng boʻlishi kerak.',
    'Value must be strictly equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"ga teng boʻlishi kerak.', // TODO: Actualize
    'Value must not be equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"ga teng boʻlmasligi kerak.',
    'Value must not be strictly equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"ga teng boʻlmasligi kerak.', // TODO: Actualize
    'Value must be greater than "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"dan katta boʻlishi kerak.',
    'Value must be greater than or equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"dan katta yoki teng boʻlishi kerak.',
    'Value must be less than "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"dan kichik boʻlishi kerak.',
    'Value must be less than or equal to "{targetValueOrAttribute}".' => 'Qiymat "{targetValueOrAttribute}"dan kichik yoki teng boʻlishi kerak.',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    'The value must be a string.' => 'Qiymat satr boʻlishi kerak.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Ruxsat berilgan turlar: integer, float va string.',
    'Value must be no less than {min}.' => 'Qiymat {min} dan kichik boʻlmasligi kerak.',
    'Value must be no greater than {max}.' => 'Qiymat {max} dan katta boʻlmasligi kerak.',
];
