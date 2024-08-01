<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\FilledAtLeast;
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
use Yiisoft\Validator\Rule\FilledOnlyOneOf;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Url;

return [
    // Used in single rule

    /** @see FilledAtLeast */
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled' => 'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} "{true}" yoki "{false}" boʻlishi kerak.',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface.' => '{Property} massiv yoki \Countable interfeysidan meros olingan boʻlishi kerak.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} kamida {min, number} ta {min, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} koʻpi bilan {max, number} ta {max, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} aynan {exactly, number} ta {exactly, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    /** @see Each */
    '{Property} must be array or iterable.' => '{Property} massiv yoki takrorlanadigan(iterable) psevdo-tur boʻlishi kerak.',
    'Every iterable key must have an integer or a string type.' => 'Barcha kalit integer yoki string turida boʻlishi kerak.',
    /** @see Email */
    '{Property} is not a valid email address.' => '{Property} yaroqli elektron pochta manzili emas.',
    /** @see In */
    '{Property} is not in the list of acceptable values.' => '{Property} qabul qilinadigan qiymatlar roʻyxatida yoʻq.',
    /** @see Ip */
    '{Property} must be a valid IP address.' => '{Property} toʻgʻri IP manzil boʻlishi kerak.',
    '{Property} must not be an IPv4 address.' => '{Property} IPv4 manzil boʻlmasligi kerak.',
    '{Property} must not be an IPv6 address.' => '{Property} IPv6 manzil boʻlmasligi kerak.',
    '{Property} contains wrong subnet mask.' => '{Property} notoʻgʻri quyi tarmoq(subnet) niqobini oʻz ichiga olgan.',
    '{Property} must be an IP address with specified subnet.' => '{Property} quyi tarmoq(subnet)ga ega IP manzil boʻlishi kerak.',
    '{Property} must not be a subnet.' => '{Property} quyi tarmoq(subnet) boʻlmasligi kerak.',
    '{Property} is not in the allowed range.' => '{Property} ruxsat etilgan manzillar qatoriga kirmaydi.',
    /** @see Integer */
    '{Property} must be an integer.' => '{Property} butun son boʻlishi kerak.',
    /** @see Json */
    '{Property} is not JSON.' => '{Property} JSON holatida emas.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} kamida {min, number} ta {min, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} koʻpi bilan {max, number} ta {max, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} {exactly, number} ta {exactly, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    /** @see Nested */
    'Nested rule without rules can be used for objects only.' => 'Qoidalarga ega boʻlmagan Nested qoidasi faqat obyektlarga ishlatilishi mumkin.',
    'An object data set data can only have an array or an object type.' => 'Obyekt maʼlumotlari obyekt boʻlishi kerak.',
    'Property "{path}" is not found.' => '{path} xususiyati topilmadi.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} raqam boʻlishi kerak.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled: {properties}.' => 'Exactly 1 property from this list must be filled: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} notoʻgʻri.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} boʻsh boʻlishi mumkin emas.',
    '{Property} not passed.' => '{Property} oʻtmadi.',
    /** @see Subset */
    '{Property} must be iterable.' => '{Property} takrorlanadigan boʻlishi kerak.',
    '{Property} is not a subset of acceptable values.' => '{Property} ruxsat etilgan qiymatlarning quyi toʻplami emas.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} "{true}" boʻlishi kerak.',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} yaroqli havola emas.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object.' => '{Property} massiv yoki obyekt boʻlishi kerak.',
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
    'The property value returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' => 'Maʼlumotlar toʻplamidan olingan qiymat skalyar turdagi boʻlishi kerak.', // TODO: Actualize
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlishi kerak.',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlishi kerak.', // TODO: Actualize
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlmasligi kerak.',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlmasligi kerak.', // TODO: Actualize
    '{Property} must be greater than "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"dan katta boʻlishi kerak.',
    '{Property} must be greater than or equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"dan katta yoki teng boʻlishi kerak.',
    '{Property} must be less than "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"dan kichik boʻlishi kerak.',
    '{Property} must be less than or equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"dan kichik yoki teng boʻlishi kerak.',
    /**
     * @see Email
     * @see Ip
     * @see Json
     * @see Length
     * @see Regex
     * @see Url
     */
    '{Property} must be a string.' => '{Property} satr boʻlishi kerak.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types are integer, float and string.' => 'Ruxsat berilgan turlar: integer, float va string.',
    '{Property} must be no less than {min}.' => '{Property} {min} dan kichik boʻlmasligi kerak.',
    '{Property} must be no greater than {max}.' => '{Property} {max} dan katta boʻlmasligi kerak.',
];
