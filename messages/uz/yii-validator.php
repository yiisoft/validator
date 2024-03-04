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
use Yiisoft\Validator\Rule\OneOf;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see AtLeast */
    'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled' => 'At least {min, number} {min, plural, one{attribute} other{attributes}} from this list must be filled',
    /** @see BooleanValue */
    '{Attribute} must be either "{true}" or "{false}".' => '{Attribute} "{true}" yoki "{false}" boʻlishi kerak.',
    /** @see Count */
    '{Attribute} must be an array or implement \Countable interface.' => '{Attribute} massiv yoki \Countable interfeysidan meros olingan boʻlishi kerak.',
    '{Attribute} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Attribute} kamida {min, number} ta {min, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Attribute} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Attribute} koʻpi bilan {max, number} ta {max, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Attribute} aynan {exactly, number} ta {exactly, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    /** @see Each */
    '{Attribute} must be array or iterable.' => '{Attribute} massiv yoki takrorlanadigan(iterable) psevdo-tur boʻlishi kerak.',
    'Every iterable key must have an integer or a string type.' => 'Barcha kalit integer yoki string turida boʻlishi kerak.',
    /** @see Email */
    '{Attribute} is not a valid email address.' => '{Attribute} yaroqli elektron pochta manzili emas.',
    /** @see In */
    '{Attribute} is not in the list of acceptable values.' => '{Attribute} qabul qilinadigan qiymatlar roʻyxatida yoʻq.',
    /** @see Ip */
    '{Attribute} must be a valid IP address.' => '{Attribute} toʻgʻri IP manzil boʻlishi kerak.',
    '{Attribute} must not be an IPv4 address.' => '{Attribute} IPv4 manzil boʻlmasligi kerak.',
    '{Attribute} must not be an IPv6 address.' => '{Attribute} IPv6 manzil boʻlmasligi kerak.',
    '{Attribute} contains wrong subnet mask.' => '{Attribute} notoʻgʻri quyi tarmoq(subnet) niqobini oʻz ichiga olgan.',
    '{Attribute} must be an IP address with specified subnet.' => '{Attribute} quyi tarmoq(subnet)ga ega IP manzil boʻlishi kerak.',
    '{Attribute} must not be a subnet.' => '{Attribute} quyi tarmoq(subnet) boʻlmasligi kerak.',
    '{Attribute} is not in the allowed range.' => '{Attribute} ruxsat etilgan manzillar qatoriga kirmaydi.',
    /** @see Integer */
    '{Attribute} must be an integer.' => '{Attribute} butun son boʻlishi kerak.',
    /** @see Json */
    '{Attribute} is not JSON.' => '{Attribute} JSON holatida emas.',
    /** @see Length */
    '{Attribute} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Attribute} kamida {min, number} ta {min, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Attribute} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Attribute} koʻpi bilan {max, number} ta {max, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Attribute} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Attribute} {exactly, number} ta {exactly, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Qoidalarga ega boʻlmagan Nested qoidasi faqat obyektlarga ishlatilishi mumkin.',
    'An object data set data can only have an array or an object type.' => 'Obyekt maʼlumotlari obyekt boʻlishi kerak.',
    'Property "{path}" is not found.' => '{path} xususiyati topilmadi.',
    /** @see Number */
    '{Attribute} must be a number.' => '{Attribute} raqam boʻlishi kerak.',
    /** @see OneOf */
    'Exactly 1 attribute from this list must be filled: {attributes}.' => 'Exactly 1 attribute from this list must be filled: {attributes}.',
    /** @see Regex */
    '{Attribute} is invalid.' => '{Attribute} notoʻgʻri.',
    /** @see Required */
    '{Attribute} cannot be blank.' => '{Attribute} boʻsh boʻlishi mumkin emas.',
    '{Attribute} not passed.' => '{Attribute} oʻtmadi.',
    /** @see Subset */
    '{Attribute} must be iterable.' => '{Attribute} takrorlanadigan boʻlishi kerak.',
    '{Attribute} is not a subset of acceptable values.' => '{Attribute} ruxsat etilgan qiymatlarning quyi toʻplami emas.',
    /** @see TrueValue */
    '{Attribute} must be "{true}".' => '{Attribute} "{true}" boʻlishi kerak.',
    /** @see Url */
    '{Attribute} is not a valid URL.' => '{Attribute} yaroqli havola emas.',

    // Used in multiple rules

    /**
     * @see AtLeast
     * @see Nested
     * @see OneOf
     */
    '{Attribute} must be an array or an object.' => '{Attribute} massiv yoki obyekt boʻlishi kerak.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types are integer, float, string, boolean. {type} given.' => 'Ruxsat berilgan turlar: integer, float, string, boolean va null. Bu qiymat turi {type}.',
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
    '{Attribute} must be equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"ga teng boʻlishi kerak.',
    '{Attribute} must be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"ga teng boʻlishi kerak.', // TODO: Actualize
    '{Attribute} must not be equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"ga teng boʻlmasligi kerak.',
    '{Attribute} must not be strictly equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"ga teng boʻlmasligi kerak.', // TODO: Actualize
    '{Attribute} must be greater than "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"dan katta boʻlishi kerak.',
    '{Attribute} must be greater than or equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"dan katta yoki teng boʻlishi kerak.',
    '{Attribute} must be less than "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"dan kichik boʻlishi kerak.',
    '{Attribute} must be less than or equal to "{targetValueOrAttribute}".' => '{Attribute} "{targetValueOrAttribute}"dan kichik yoki teng boʻlishi kerak.',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Attribute} must be a string.' => '{Attribute} satr boʻlishi kerak.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Ruxsat berilgan turlar: integer, float va string.',
    '{Attribute} must be no less than {min}.' => '{Attribute} {min} dan kichik boʻlmasligi kerak.',
    '{Attribute} must be no greater than {max}.' => '{Attribute} {max} dan katta boʻlmasligi kerak.',
];
