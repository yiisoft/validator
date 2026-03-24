<?php

declare(strict_types=1);

use Yiisoft\Validator\Rule\AnyRule;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\FilledOnlyOneOf;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\Rule\Image\Image;
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
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\Type\BooleanType;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Rule\Type\StringType;
use Yiisoft\Validator\Rule\UniqueIterable;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\Uuid;

return [
    // Used in single rule

    /** @see FilledAtLeast */
    'At least {min, number} {min, plural, one{property} other{properties}} from this list must be filled for {property}: {properties}.' =>
        'Bu roʻyxatdan kamida {min, number} ta xususiyat {property} uchun toʻldirilishi kerak: {properties}.',
    /** @see BooleanValue */
    '{Property} must be either "{true}" or "{false}".' => '{Property} "{true}" yoki "{false}" boʻlishi kerak.',
    /** @see Count */
    '{Property} must be an array or implement \Countable interface. {type} given.' => '{Property} massiv yoki \Countable interfeysidan meros olingan boʻlishi kerak. {type} berilgan.',
    '{Property} must contain at least {min, number} {min, plural, one{item} other{items}}.' => '{Property} kamida {min, number} ta {min, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Property} must contain at most {max, number} {max, plural, one{item} other{items}}.' => '{Property} koʻpi bilan {max, number} ta {max, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} other{items}}.' => '{Property} aynan {exactly, number} ta {exactly, plural, one{element} other{element}}dan iborat boʻlishi kerak.',
    /** @see Each */
    '{Property} must be array or iterable. {type} given.' => '{Property} massiv yoki takrorlanadigan(iterable) psevdo-tur boʻlishi kerak. {type} berilgan.',
    'Every iterable key of {property} must have an integer or a string type. {type} given.' => '{property} ning barcha kaliti integer yoki string turida boʻlishi kerak. {type} berilgan.',
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
    /**
     * @see IntegerType
     * @see Integer
     */
    '{Property} must be an integer.' => '{Property} butun son boʻlishi kerak.',
    /** @see Json */
    '{Property} is not a valid JSON.' => '{Property} yaroqli JSON emas.',
    /** @see Length */
    '{Property} must contain at least {min, number} {min, plural, one{character} other{characters}}.' => '{Property} kamida {min, number} ta {min, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Property} must contain at most {max, number} {max, plural, one{character} other{characters}}.' => '{Property} koʻpi bilan {max, number} ta {max, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    '{Property} must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' => '{Property} {exactly, number} ta {exactly, plural, one{belgi} other{belgi}}dan iborat boʻlishi kerak.',
    /** @see Nested */
    'Nested rule without rules requires {property} to be an object. {type} given.' => 'Qoidalarga ega boʻlmagan Nested qoidasi {property} obyekt boʻlishini talab qiladi. {type} berilgan.',
    'An object data set data for {property} can only have an array type. {type} given.' => '{property} uchun obyekt maʼlumotlari faqat massiv turida boʻlishi mumkin. {type} berilgan.',
    'Property "{path}" is not found in {property}.' => '"{path}" xususiyati {property} da topilmadi.',
    /** @see Number */
    '{Property} must be a number.' => '{Property} raqam boʻlishi kerak.',
    /** @see FilledOnlyOneOf */
    'Exactly 1 property from this list must be filled for {property}: {properties}.' => 'Bu roʻyxatdan aynan 1 ta xususiyat {property} uchun toʻldirilishi kerak: {properties}.',
    /** @see Regex */
    '{Property} is invalid.' => '{Property} notoʻgʻri.',
    /** @see Required */
    '{Property} cannot be blank.' => '{Property} boʻsh boʻlishi mumkin emas.',
    '{Property} not passed.' => '{Property} oʻtmadi.',
    /** @see StringValue */
    '{Property} must be a string.' => '{Property} satr boʻlishi kerak.',
    /** @see Subset */
    '{Property} must be iterable. {type} given.' => '{Property} takrorlanadigan boʻlishi kerak. {type} berilgan.',
    '{Property} is not a subset of acceptable values.' => '{Property} ruxsat etilgan qiymatlarning quyi toʻplami emas.',
    /** @see TrueValue */
    '{Property} must be "{true}".' => '{Property} "{true}" boʻlishi kerak.',
    /** @see Url */
    '{Property} is not a valid URL.' => '{Property} yaroqli havola emas.',
    /** @see Uuid */
    'The value of {property} is not a valid UUID.' => '{property} qiymati yaroqli UUID emas.',

    // Used in multiple rules

    /**
     * @see FilledAtLeast
     * @see Nested
     * @see FilledOnlyOneOf
     */
    '{Property} must be an array or an object. {type} given.' => '{Property} massiv yoki obyekt boʻlishi kerak. {type} berilgan.',
    /**
     * @see BooleanValue
     * @see TrueValue
     */
    'The allowed types for {property} are integer, float, string, boolean. {type} given.' => '{property} uchun ruxsat berilgan turlar: integer, float, string, boolean. {type} berilgan.',
    /**
     * @see Compare
     * @see Equal
     * @see GreaterThan
     * @see GreaterThanOrEqual
     * @see LessThan
     * @see LessThanOrEqual
     * @see NotEqual
     */
    'The allowed types for {property} are integer, float, string, boolean, null and object implementing \Stringable interface or \DateTimeInterface. {type} given.' =>
        '{property} uchun ruxsat berilgan turlar: integer, float, string, boolean, null va \Stringable yoki \DateTimeInterface interfeysini amalga oshirgan obyekt. {type} berilgan.',
    '{Property} returned from a custom data set must have one of the following types: integer, float, string, boolean, null or an object implementing \Stringable interface or \DateTimeInterface.' =>
        'Maxsus maʼlumotlar toʻplamidan qaytarilgan {Property} quyidagi turlardan biriga ega boʻlishi kerak: integer, float, string, boolean, null yoki \Stringable yoki \DateTimeInterface interfeysini amalga oshirgan obyekt.',
    '{Property} must be equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlishi kerak.',
    '{Property} must be strictly equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga qatʼiy teng boʻlishi kerak.',
    '{Property} must not be equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga teng boʻlmasligi kerak.',
    '{Property} must not be strictly equal to "{targetValueOrProperty}".' => '{Property} "{targetValueOrProperty}"ga qatʼiy teng boʻlmasligi kerak.',
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
     * @see StringType
     * @see Url
     * @see Uuid
     */
    '{Property} must be a string. {type} given.' => '{Property} satr boʻlishi kerak. {type} berilgan.',
    /**
     * @see Number
     * @see Integer
     */
    'The allowed types for {property} are integer, float and string. {type} given.' => '{property} uchun ruxsat berilgan turlar: integer, float va string. {type} berilgan.',
    '{Property} must be no less than {min}.' => '{Property} {min} dan kichik boʻlmasligi kerak.',
    '{Property} must be no greater than {max}.' => '{Property} {max} dan katta boʻlmasligi kerak.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be no earlier than {limit}.' => '{Property} {limit} dan oldin boʻlmasligi kerak.',
    '{Property} must be no later than {limit}.' => '{Property} {limit} dan keyin boʻlmasligi kerak.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Date
     * @see \Yiisoft\Validator\Rule\Date\DateTime
     */
    '{Property} must be a date.' => '{Property} sana boʻlishi kerak.',

    /**
     * @see \Yiisoft\Validator\Rule\Date\Time
     */
    '{Property} must be a time.' => '{Property} vaqt boʻlishi kerak.',

    /** @see UniqueIterable */
    '{Property} must be array or iterable.' => '{Property} massiv yoki takrorlanadigan boʻlishi kerak.',
    'The allowed types for iterable\'s item values of {property} are integer, float, string, boolean and object implementing \Stringable or \DateTimeInterface.' =>
        '{property} ning takrorlanadigan element qiymatlari uchun ruxsat berilgan turlar: integer, float, string, boolean va \Stringable yoki \DateTimeInterface interfeysini amalga oshirgan obyekt.',
    'All iterable items of {property} must have the same type.' =>
        '{property} ning barcha takrorlanadigan elementlari bir xil turda boʻlishi kerak.',
    'Every iterable\'s item of {property} must be unique.' => '{property} ning har bir takrorlanadigan elementi yagona boʻlishi kerak.',

    /** @see BooleanType */
    '{Property} must be a boolean.' => '{Property} mantiqiy qiymat boʻlishi kerak.',
    /** @see FloatType */
    '{Property} must be a float.' => '{Property} oʻnlik kasr boʻlishi kerak.',
    /** @see AnyRule */
    'At least one of the inner rules must pass the validation.' => 'Ichki qoidalardan kamida bittasi tekshiruvdan oʻtishi kerak.',

    /** @see Image */
    '{Property} must be an image.' => '{Property} rasm boʻlishi kerak.',
    'The width of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        '{property} kengligi aynan {exactly, number} {exactly, plural, one{piksel} other{piksel}} boʻlishi kerak.',
    'The height of {property} must be exactly {exactly, number} {exactly, plural, one{pixel} other{pixels}}.' =>
        '{property} balandligi aynan {exactly, number} {exactly, plural, one{piksel} other{piksel}} boʻlishi kerak.',
    'The width of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        '{property} kengligi {limit, number} {limit, plural, one{piksel} other{piksel}}dan kichik boʻlishi mumkin emas.',
    'The height of {property} cannot be smaller than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        '{property} balandligi {limit, number} {limit, plural, one{piksel} other{piksel}}dan kichik boʻlishi mumkin emas.',
    'The width of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        '{property} kengligi {limit, number} {limit, plural, one{piksel} other{piksel}}dan katta boʻlishi mumkin emas.',
    'The height of {property} cannot be larger than {limit, number} {limit, plural, one{pixel} other{pixels}}.' =>
        '{property} balandligi {limit, number} {limit, plural, one{piksel} other{piksel}}dan katta boʻlishi mumkin emas.',
    'The aspect ratio of {property} must be {aspectRatioWidth, number}:{aspectRatioHeight, number} with margin {aspectRatioMargin, number}%.' =>
        '{property} tomonlar nisbati {aspectRatioWidth, number}:{aspectRatioHeight, number} boʻlishi kerak, {aspectRatioMargin, number}% farq bilan.',
];
